<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\landlord\TenantDatabase;

/**
 * Inserts/updates landlord tenant_databases pool rows (Hostinger pre-created MySQL DB names).
 * Used by Artisan — avoids Database\Seeders\* which may be missing on minimal deploys.
 */
final class TenantDatabasePoolTableSeeder
{
    /**
     * @param  list<string>  $names  Exact MySQL database names from hPanel
     */
    public function run(array $names): void
    {
        foreach ($names as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }
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
