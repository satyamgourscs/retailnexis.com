<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\TenantDatabasePoolExhaustedException;
use App\Models\landlord\TenantDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

final class TenantDatabasePoolAllocator
{
    /**
     * Reserve the next free pool database for this tenant and set Stancl internal db_name.
     *
     * @throws TenantDatabasePoolExhaustedException
     */
    public function allocateForTenant(TenantWithDatabase $tenant): void
    {
        if (! config('tenancy.database.pool_enabled')) {
            return;
        }

        $connection = (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');

        DB::connection($connection)->transaction(function () use ($tenant, $connection): void {
            $row = TenantDatabase::on($connection)
                ->where('is_used', false)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new TenantDatabasePoolExhaustedException();
            }

            $tenantKey = (string) $tenant->getTenantKey();

            $row->forceFill([
                'is_used' => true,
                'tenant_id' => $tenantKey,
                'assigned_at' => now(),
            ])->save();

            $tenant->setInternal('db_name', $row->name);

            Log::info('tenant_database_pool.assigned', [
                'tenant_id' => $tenantKey,
                'database' => $row->name,
                'pool_row_id' => $row->id,
            ]);
        });
    }

    /**
     * Mark pool row free when tenant DB is "deleted" (pool mode: no DROP DATABASE).
     */
    public function releaseForTenant(TenantWithDatabase $tenant): void
    {
        if (! config('tenancy.database.pool_enabled')) {
            return;
        }

        $connection = (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');
        $key = (string) $tenant->getTenantKey();

        $updated = TenantDatabase::on($connection)
            ->where('tenant_id', $key)
            ->update([
                'is_used' => false,
                'tenant_id' => null,
                'assigned_at' => null,
            ]);

        if ($updated) {
            Log::info('tenant_database_pool.released', [
                'tenant_id' => $key,
            ]);
        }
    }
}
