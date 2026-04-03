<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Bootstrappers\SafeCacheTenancyBootstrapper;
use App\Jobs\DispatchFinalizeNewTenantJob;
use App\Jobs\TenantCreatedJobPipeline;
use App\Jobs\TenantMigrateDatabase;
use App\Listeners\ProvisionMissingTenantDatabase;
use App\Bootstrappers\SafeFilesystemTenancyBootstrapper;
use App\Tenancy\CustomDomainTenantResolver;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    // By default, no namespace is used to support the callable array syntax.
    public static string $controllerNamespace = '';

    public function register(): void
    {
        $this->app->bind(DomainTenantResolver::class, CustomDomainTenantResolver::class);

        // Stancl: queued pipelines default off unless explicitly enabled in config.
        JobPipeline::$shouldBeQueuedByDefault = false;

        // Stale config:cache often still lists FilesystemTenancyBootstrapper; force the safe subclass.
        $this->app->singleton(FilesystemTenancyBootstrapper::class, static function ($app) {
            return $app->make(SafeFilesystemTenancyBootstrapper::class);
        });

        // Stateful bootstrappers: same instance for bootstrap + revert when possible.
        $this->app->singleton(SafeFilesystemTenancyBootstrapper::class);
        $this->app->singleton(SafeCacheTenancyBootstrapper::class);

        $this->app->booting(function () {
            foreach ((array) config('tenancy.filesystem.disks', []) as $disk) {
                SafeFilesystemTenancyBootstrapper::seedCentralDiskRoot(
                    (string) $disk,
                    config("filesystems.disks.{$disk}.root")
                );
            }
        });

        // Stale `php artisan config:cache` may omit database.connections.{template} (e.g. local) while
        // tenancy still points at it → "Undefined array key local". Clone retailnexis_tenant/mysql as fallback.
        $this->ensureTenancyTemplateDatabaseConnectionExists();
    }

    protected function ensureTenancyTemplateDatabaseConnectionExists(): void
    {
        $template = (string) config('tenancy.database.template_tenant_connection', 'local');
        if ($template === '') {
            return;
        }

        $connections = config('database.connections', []);
        if (isset($connections[$template]) && is_array($connections[$template])) {
            return;
        }

        $fallback = $connections['retailnexis_tenant'] ?? $connections['mysql'] ?? null;
        if (! is_array($fallback)) {
            return;
        }

        config(["database.connections.{$template}" => $fallback]);
    }

    public function events()
    {
        return [
            // Tenant events
            Events\CreatingTenant::class => [],
            Events\TenantCreated::class => [
                TenantCreatedJobPipeline::make([
                    Jobs\CreateDatabase::class,
                    TenantMigrateDatabase::class,
                    DispatchFinalizeNewTenantJob::class,
                ])->send(function (Events\TenantCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued((bool) config('tenancy.provisioning_uses_queue', false)),
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [
                JobPipeline::make([
                    Jobs\DeleteDatabase::class,
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(false), // `false` by default, but you probably want to make this `true` for production.
            ],

            // Domain events
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            // Database events
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Tenancy events
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],

            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],

            Events\BootstrappingTenancy::class => [
                ProvisionMissingTenantDatabase::class,
            ],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Resource syncing
            Events\SyncedResourceSaved::class => [
                Listeners\UpdateSyncedResource::class,
            ],

            // Fired only when a synced resource is changed in a different DB than the origin DB (to avoid infinite loops)
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    public function boot()
    {
        // Must use config(), not env(): when config is cached, env() is null and tenant routes never load.
        if (empty(config('app.landlord_db'))) {
            return;
        }

        // Always swap Stancl's filesystem bootstrapper for Safe* (avoids parent's revert() on wrong instances).
        $this->app->make(Tenancy::class)->getBootstrappersUsing = static function ($tenant) {
            $classes = (array) config('tenancy.bootstrappers', []);

            return array_map(static function ($class) {
                $class = (string) $class;

                return $class === FilesystemTenancyBootstrapper::class
                    ? SafeFilesystemTenancyBootstrapper::class
                    : $class;
            }, $classes);
        };

        $this->bootEvents();
        $this->mapRoutes();

        $this->makeTenancyMiddlewareHighestPriority();
    }

    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    // Avoid dispatch_sync(): Stancl's QueueTenancyBootstrapper calls tenancy()->end() on
                    // JobProcessing when payload has no tenant_id; JobProcessed does not restore tenant HTTP context.
                    $pipeline = $listener;
                    $listener = static function (...$args) use ($pipeline): void {
                        $executable = $pipeline->executable($args);
                        if ($pipeline->shouldBeQueued) {
                            dispatch($executable);
                        } else {
                            $executable->handle();
                        }
                    };
                }

                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes()
    {
        if (file_exists(base_path('routes/tenant.php'))) {
            Route::namespace(static::$controllerNamespace)
                ->group(base_path('routes/tenant.php'));
        }
    }

    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            // Even higher priority than the initialization middleware
            Middleware\PreventAccessFromCentralDomains::class,

            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }
}
