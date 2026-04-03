<?php

declare(strict_types=1);

namespace App\Tenancy;

use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Jobs\CreateDatabase;
use App\Jobs\TenantMigrateDatabase;

/**
 * Fallback when the tenant row exists but the physical database was never created
 * (failed job, manual DB deletion, pre-migration tenants). Runs before DB bootstrap.
 */
class TenantDatabaseProvisioner
{
    public function ensureDatabasePresent(TenantWithDatabase $tenant): void
    {
        if (! config('tenancy.auto_provision_missing_database', true)) {
            return;
        }

        if ($tenant->getInternal('create_database') === false) {
            return;
        }

        $name = $tenant->database()->getName();

        if ($tenant->database()->manager()->databaseExists($name)) {
            return;
        }

        Log::warning('TENANT DB missing; auto-provisioning (create + migrate)', [
            'tenant' => (string) $tenant->getTenantKey(),
            'database' => $name,
        ]);

        // Do not use Bus::dispatchSync: Stancl's QueueTenancyBootstrapper ends tenancy on
        // JobProcessing when payload has no tenant_id, and JobProcessed does not restore it.
        (new CreateDatabase($tenant))->handle(app(DatabaseManager::class));
        (new TenantMigrateDatabase($tenant))->handle();
    }
}
