<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * Tenant migrations (equivalent to Stancl\Tenancy\Jobs\MigrateDatabase with --force + strict exit code).
 */
class TenantMigrateDatabase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public function __construct(
        protected TenantWithDatabase $tenant
    ) {
    }

    public function handle(): void
    {
        @set_time_limit(0);

        $tenantKey = (string) $this->tenant->getTenantKey();

        Log::info('TENANT MIGRATE', ['tenant' => $tenantKey]);

        $exitCode = Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantKey],
            '--force' => true,
        ]);

        Log::info('TENANT MIGRATE DONE', [
            'tenant' => $tenantKey,
            'exit_code' => $exitCode,
            'output' => Artisan::output(),
        ]);

        if ($exitCode !== 0) {
            throw new \RuntimeException('tenants:migrate exited with code '.$exitCode.': '.Artisan::output());
        }

        // Do not call tenancy()->end() here. Tenancy::runForMultiple() already restores the previous
        // tenant or ends tenancy when there was no outer context. Calling end() after that wipes the
        // tenant while still inside BootstrapTenancy (ProvisionMissingTenantDatabase → migrate), which
        // leads to DatabaseTenancyBootstrapper::bootstrap(null).
    }
}
