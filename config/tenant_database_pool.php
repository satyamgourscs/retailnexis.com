<?php

declare(strict_types=1);

return [
    /*
    | Comma-separated MySQL database names pre-created in hPanel (same user as DB_USERNAME).
    | Used by: php artisan tenant:seed-database-pool
    */
    'names' => array_values(array_filter(
        array_map(
            'trim',
            explode(',', (string) env(
                'TENANT_DATABASE_POOL_NAMES',
                'u612565959_tenant_pool_01,u612565959_tenant_pool_02,u612565959_tenant_pool_03'
            ))
        ),
        static fn (string $n): bool => $n !== ''
    )),
];
