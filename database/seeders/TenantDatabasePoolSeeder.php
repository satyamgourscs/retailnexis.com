<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\landlord\TenantDatabase;
use Illuminate\Database\Seeder;

/**
 * Example rows for tenant_databases — replace database names with real hPanel-created schemas.
 *
 * Run after migrate and after creating empty MySQL databases in Hostinger:
 *   php artisan db:seed --class="Database\Seeders\TenantDatabasePoolSeeder"
 *
 * If "Target class does not exist": upload this file, then run: composer dump-autoload -o
 */
class TenantDatabasePoolSeeder extends Seeder
{
    public function run(): void
    {
        // Replace with your actual pre-created DB names (same user as DB_USERNAME must have ALL privileges).
        $pool = [
            'u612565959_tenant_pool_01',
            'u612565959_tenant_pool_02',
            'u612565959_tenant_pool_03',
        ];

        foreach ($pool as $name) {
            TenantDatabase::query()->updateOrCreate(
                ['name' => $name],
                [
                    'is_used' => false,
                    'tenant_id' => null,
                    'assigned_at' => null,
                ]
            );
        }
    }
}
