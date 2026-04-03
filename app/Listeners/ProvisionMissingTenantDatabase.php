<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Tenancy\TenantDatabaseProvisioner;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Events\BootstrappingTenancy;

/**
 * Runs before DatabaseTenancyBootstrapper::bootstrap so "database does not exist" is healed on first request.
 */
class ProvisionMissingTenantDatabase
{
    public function __construct(
        protected TenantDatabaseProvisioner $provisioner
    ) {
    }

    public function handle(BootstrappingTenancy $event): void
    {
        $tenant = $event->tenancy->tenant;

        if (! $tenant instanceof TenantWithDatabase) {
            return;
        }

        $this->provisioner->ensureDatabasePresent($tenant);
    }
}
