<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

return [
    'tenant_model' => \App\Models\landlord\Tenant::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => Domain::class,

    /**
     * The list of domains hosting your central app.
     *
     * Only relevant if you're using the domain or subdomain identification middleware.
     */
    // Normalize CENTRAL_DOMAIN so PreventAccessFromCentralDomains can match reliably.
    // Handles cases like "https://tryonedigital.com" and "www.tryonedigital.com".
    'central_domains' => (function () {
        $centralDomain = trim((string) env('CENTRAL_DOMAIN', ''));
        $appUrlHost = parse_url((string) env('APP_URL', ''), PHP_URL_HOST);

        if ($centralDomain !== '') {
            // Remove scheme, path and port if accidentally included in env.
            $centralDomain = preg_replace('#^https?://#i', '', $centralDomain);
            $centralDomain = preg_replace('#/.*$#', '', $centralDomain);
            $centralDomain = explode(':', $centralDomain)[0];
            $centralDomain = trim($centralDomain);
            $centralDomain = strtolower(rtrim($centralDomain, '.'));
        }

        if (is_string($appUrlHost) && $appUrlHost !== '') {
            $appUrlHost = strtolower(rtrim($appUrlHost, '.'));
        }

        // Put configured central domain first so named routes generate URLs on the same host.
        $domains = [];
        if ($centralDomain !== '') {
            $domains[] = $centralDomain;
            if ($centralDomain !== 'localhost' && $centralDomain !== '127.0.0.1') {
                $domains[] = 'www.' . $centralDomain;
            } else {
                // Local convenience: keep www.localhost only after localhost.
                $domains[] = 'www.localhost';
            }
        }

        if (is_string($appUrlHost) && $appUrlHost !== '') {
            $domains[] = $appUrlHost;
            if ($appUrlHost !== 'localhost' && $appUrlHost !== '127.0.0.1') {
                $domains[] = 'www.' . $appUrlHost;
            }
        }

        // Local fallback domains (kept after central domain to avoid host switching).
        $domains[] = 'localhost';
        $domains[] = '127.0.0.1';

        return array_values(array_unique(array_filter($domains)));
    })(),

    /**
     * Tenancy bootstrappers are executed when tenancy is initialized.
     * Their responsibility is making Laravel features tenant-aware.
     *
     * To configure their behavior, see the config keys below.
     */
    /**
     * Tenancy bootstrappers.
     *
     * CacheTenancyBootstrapper uses `cache()->tags(...)` internally.
     * Some cache stores (e.g. file/array) do NOT support tags and will throw:
     * "This cache store does not support tagging."
     */
    'bootstrappers' => (function () {
        $cacheDriver = (string) env('CACHE_DRIVER', config('cache.default', 'file'));
        $cacheSupportsTags = in_array(strtolower($cacheDriver), ['redis', 'memcached', 'dynamodb', 'apc'], true);

        $bootstrappers = [
            Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
            Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
            Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
            // Stancl\Tenancy\Bootstrappers\RedisTenancyBootstrapper::class, // Note: phpredis is needed
        ];

        if ($cacheSupportsTags) {
            $bootstrappers[] = Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class;
        }

        return $bootstrappers;
    })(),

    /**
     * Database tenancy config. Used by DatabaseTenancyBootstrapper.
     */
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'saleprosaas_landlord'),

        /**
         * Connection used as a "template" for the dynamically created tenant database connection.
         * Note: don't name your template connection tenant. That name is reserved by package.
         */
        'template_tenant_connection' => 'saleprosaas_tenant',

        /**
         * Tenant database names are created like this:
         * prefix + tenant_id + suffix.
         */
        'prefix' => env('DB_PREFIX'),
        'suffix' => '',

        /**
         * TenantDatabaseManagers are classes that handle the creation & deletion of tenant databases.
         */
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => App\CustomMySQLDatabaseManager\CustomMySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,

        /**
         * Use this database manager for MySQL to have a DB user created for each tenant database.
         * You can customize the grants given to these users by changing the $grants property.
         */
            // 'mysql' => Stancl\Tenancy\TenantDatabaseManagers\PermissionControlledMySQLDatabaseManager::class,

        /**
         * Disable the pgsql manager above, and enable the one below if you
         * want to separate tenant DBs by schemas rather than databases.
         */
            // 'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLSchemaManager::class, // Separate by schema instead of database
        ],
    ],

    /**
     * Cache tenancy config. Used by CacheTenancyBootstrapper.
     *
     * This works for all Cache facade calls, cache() helper
     * calls and direct calls to injected cache stores.
     *
     * Each key in cache will have a tag applied on it. This tag is used to
     * scope the cache both when writing to it and when reading from it.
     *
     * You can clear cache selectively by specifying the tag.
     */
    'cache' => [
        'tag_base' => 'tenant', // This tag_base, followed by the tenant_id, will form a tag that will be applied on each cache call.
    ],

    /**
     * Filesystem tenancy config. Used by FilesystemTenancyBootstrapper.
     * https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/#filesystem-tenancy-boostrapper.
     */
    'filesystem' => [
        /**
         * Each disk listed in the 'disks' array will be suffixed by the suffix_base, followed by the tenant_id.
         */
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
            // 's3',
        ],

        /**
         * Use this for local disks.
         *
         * See https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/#filesystem-tenancy-boostrapper
         */
        'root_override' => [
            // Disks whose roots should be overriden after storage_path() is suffixed.
            'local' => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],

        /**
         * Should storage_path() be suffixed.
         *
         * Note: Disabling this will likely break local disk tenancy. Only disable this if you're using an external file storage service like S3.
         *
         * For the vast majority of applications, this feature should be enabled. But in some
         * edge cases, it can cause issues (like using Passport with Vapor - see #196), so
         * you may want to disable this if you are experiencing these edge case issues.
         */
        'suffix_storage_path' => true,

        /**
         * By default, asset() calls are made multi-tenant too. You can use global_asset() and mix()
         * for global, non-tenant-specific assets. However, you might have some issues when using
         * packages that use asset() calls inside the tenant app. To avoid such issues, you can
         * disable asset() helper tenancy and explicitly use tenant_asset() calls in places
         * where you want to use tenant-specific assets (product images, avatars, etc).
         */
        'asset_helper_tenancy' => true,
    ],

    /**
     * Redis tenancy config. Used by RedisTenancyBoostrapper.
     *
     * Note: You need phpredis to use Redis tenancy.
     *
     * Note: You don't need to use this if you're using Redis only for cache.
     * Redis tenancy is only relevant if you're making direct Redis calls,
     * either using the Redis facade or by injecting it as a dependency.
     */
    'redis' => [
        'prefix_base' => 'tenant', // Each key in Redis will be prepended by this prefix_base, followed by the tenant id.
        'prefixed_connections' => [ // Redis connections whose keys are prefixed, to separate one tenant's keys from another.
            // 'default',
        ],
    ],

    /**
     * Features are classes that provide additional functionality
     * not needed for tenancy to be bootstrapped. They are run
     * regardless of whether tenancy has been initialized.
     *
     * See the documentation page for each class to
     * understand which ones you want to enable.
     */
    'features' => [
        // Stancl\Tenancy\Features\UserImpersonation::class,
        // Stancl\Tenancy\Features\TelescopeTags::class,
        // EMERGENCY: UniversalRoutes can apply tenancy middleware broadly and break
        // central auth/CSRF. Keep it disabled until central domain is stable.
        // Stancl\Tenancy\Features\UniversalRoutes::class,
        // Stancl\Tenancy\Features\TenantConfig::class, // https://tenancyforlaravel.com/docs/v3/features/tenant-config
        // Stancl\Tenancy\Features\CrossDomainRedirect::class, // https://tenancyforlaravel.com/docs/v3/features/cross-domain-redirect
    ],

    /**
     * Should tenancy routes be registered.
     *
     * Tenancy routes include tenant asset routes. By default, this route is
     * enabled. But it may be useful to disable them if you use external
     * storage (e.g. S3 / Dropbox) or have a custom asset controller.
     */
    'routes' => true,

    /**
     * Parameters used by the tenants:migrate command.
     */
    'migration_parameters' => [
        '--force' => true, // This needs to be true to run migrations in production.
        '--path' => [
            database_path('migrations/tenant'),
            base_path('Modules/Manufacturing/Database/Migrations'),
            base_path('Modules/Woocommerce/Database/Migrations'),
            base_path('Modules/Ecommerce/Database/Migrations'),
            //base_path('Modules/Restaurant/Database/Migrations'),
        ],
        '--realpath' => true,
    ],

    /**
     * Parameters used by the tenants:seed command.
     */
    'seeder_parameters' => [
        '--class' => Database\Seeders\Tenant\TenantDatabaseSeeder::class, // root seeder class
        '--force' => true,
    ],
];
