<?php

declare(strict_types=1);

namespace App\Bootstrappers;

use App\Tenancy\TenantSafeCacheManager;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class SafeCacheTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var CacheManager|null */
    protected $originalCache;

    public function __construct(
        protected Application $app
    ) {}

    public function bootstrap(Tenant $tenant): void
    {
        $this->resetFacadeCache();

        $this->originalCache = $this->originalCache ?? $this->app['cache'];
        $this->app->extend('cache', function () {
            return new TenantSafeCacheManager($this->app);
        });
    }

    public function revert(): void
    {
        $this->resetFacadeCache();

        $this->app->extend('cache', function () {
            return $this->originalCache;
        });

        $this->originalCache = null;
    }

    public function resetFacadeCache(): void
    {
        Cache::clearResolvedInstances();
    }
}
