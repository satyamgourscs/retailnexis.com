<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Referenced by some deploy notes as:
 *   php artisan db:seed --class=TenantSeeder
 *
 * Landlord clients/rows live in the central DB and are normally created via the
 * superadmin UI or jobs — not this seeder. Tenant *application* DBs use Stancl, e.g.:
 *   php artisan tenants:seed --class="Database\\Seeders\\Tenant\\TenantDatabaseSeeder"
 *
 * This class exists so the artisan command resolves; it performs no writes by default.
 */
class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Intentionally empty — avoid inserting demo tenants into production landlord DB.
    }
}
