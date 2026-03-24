<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * After importing the landlord DB from SQL, `migrations` may be empty while tables exist.
 * `php artisan migrate` then fails with "Table 'languages' already exists".
 * This command inserts migration rows only for create_* / crete_* migrations whose table already exists.
 */
class LandlordRegisterExistingTablesCommand extends Command
{
    protected $signature = 'landlord:migrations:register-existing-tables
                            {--force : Do not ask for confirmation}';

    protected $description = 'Record create_*_table migrations as run when the table already exists (SQL import / out-of-sync migrations table).';

    public function handle(): int
    {
        $connection = 'saleprosaas_landlord';

        if (! Schema::connection($connection)->hasTable('migrations')) {
            $this->error('No migrations table. Run: php artisan migrate --path=database/migrations/landlord (may fail once; then run this command).');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Register create-table migrations for tables that already exist?', true)) {
            return self::SUCCESS;
        }

        $path = database_path('migrations/landlord');
        $files = glob($path.'/*.php') ?: [];
        sort($files);

        $batch = (int) DB::connection($connection)->table('migrations')->max('batch');
        $batch = max(1, $batch + 1);

        $inserted = 0;
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (DB::connection($connection)->table('migrations')->where('migration', $name)->exists()) {
                continue;
            }

            $table = $this->inferTableFromMigrationName($name);
            if ($table === null) {
                continue;
            }

            if (! Schema::connection($connection)->hasTable($table)) {
                continue;
            }

            DB::connection($connection)->table('migrations')->insert([
                'migration' => $name,
                'batch' => $batch,
            ]);
            $this->line("Registered (table exists): {$name}");
            $inserted++;
        }

        $this->info("Inserted {$inserted} migration row(s). Next: php artisan migrate --path=database/migrations/landlord --force");

        return self::SUCCESS;
    }

    private function inferTableFromMigrationName(string $migrationName): ?string
    {
        if (preg_match('/_(?:create|crete)_([a-z0-9_]+)_table$/i', $migrationName, $m)) {
            return $m[1];
        }

        return null;
    }
}
