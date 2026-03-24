<?php

use App\Services\TenantDatabasePoolTableSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| tenant:seed-database-pool is defined here (not app/Console/Commands) so
| Hostinger deploys that only need this file + TenantDatabasePoolTableSeeder.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('tenant:seed-database-pool {--names= : Comma-separated MySQL database names}', function () {
    /** @var \Illuminate\Console\Command $this */
    $raw = $this->option('names');
    $list = is_string($raw) && $raw !== ''
        ? array_values(array_filter(array_map('trim', explode(',', $raw)), static fn (string $n): bool => $n !== ''))
        : config('tenant_database_pool.names', []);

    if ($list === []) {
        $this->error('No database names. Set TENANT_DATABASE_POOL_NAMES in .env or use --names=db1,db2');

        return 1;
    }

    app(TenantDatabasePoolTableSeeder::class)->run($list);
    $this->info('tenant_databases updated: '.count($list).' row(s).');

    return 0;
})->purpose('Register pre-created tenant MySQL databases in tenant_databases (Hostinger pool)');
