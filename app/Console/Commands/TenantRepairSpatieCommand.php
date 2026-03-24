<?php

namespace App\Console\Commands;

use App\Models\landlord\Tenant;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Diagnose / repair missing Spatie permission pivot on a single tenant database.
 */
class TenantRepairSpatieCommand extends Command
{
    protected $signature = 'tenants:repair-spatie {tenant : Tenant id (same as subdomain key, e.g. demoCompanyName)}';

    protected $description = 'Check permissions/roles tables and create role_has_permissions if missing';

    public function handle(): int
    {
        $id = (string) $this->argument('tenant');
        $tenant = Tenant::find($id);
        if (! $tenant) {
            $this->error("Tenant «{$id}» not found in landlord `tenants` table.");

            return self::FAILURE;
        }

        $status = [
            'permissions' => false,
            'roles' => false,
            'role_has_permissions' => false,
        ];

        $tenant->run(function () use (&$status) {
            $status['permissions'] = Schema::hasTable('permissions');
            $status['roles'] = Schema::hasTable('roles');
            $status['role_has_permissions'] = Schema::hasTable('role_has_permissions');
        });

        $this->line('Tenant: '.$id);
        $this->line('  permissions:            '.($status['permissions'] ? 'ok' : 'MISSING'));
        $this->line('  roles:                  '.($status['roles'] ? 'ok' : 'MISSING'));
        $this->line('  role_has_permissions:   '.($status['role_has_permissions'] ? 'ok' : 'MISSING'));

        if (! $status['permissions'] || ! $status['roles']) {
            $this->newLine();
            $this->warn('Base tables are missing — migrations never finished for this tenant (often a timeout).');
            $this->warn('Recommended: Superadmin → Clients → delete this client (drops DB), then create again.');
            $this->warn('Destructive CLI alternative: php artisan tenants:migrate-fresh --tenants='.$id.' --force');

            return self::FAILURE;
        }

        if ($status['role_has_permissions']) {
            $this->info('Nothing to repair.');

            return self::SUCCESS;
        }

        $tenant->run(function () {
            if (! Schema::hasColumn('roles', 'guard_name')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->string('guard_name')->nullable();
                });
            }

            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedInteger('permission_id');
                $table->unsignedInteger('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'role_id']);
            });

            try {
                app('cache')->forget('spatie.permission.cache');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        $this->info('Created table `role_has_permissions`. Re-run seed if data is incomplete:');
        $this->line('  php artisan tenants:seed --tenants='.$id.' --force');

        return self::SUCCESS;
    }
}
