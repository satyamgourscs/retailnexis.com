<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs tenant DBs where `role_has_permissions` is missing (e.g. interrupted migrate / timeout).
 *
 * Run for one tenant only if full migrate fails on "table already exists":
 * php artisan tenants:migrate --tenants=YOUR_TENANT_ID --force --path=database/migrations/tenant/2026_03_20_120000_ensure_spatie_role_has_permissions_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_has_permissions')) {
            return;
        }

        $hasPermissions = Schema::hasTable('permissions');
        $hasRoles = Schema::hasTable('roles');
        if (! $hasPermissions || ! $hasRoles) {
            // Tenant DB never completed core migrations; do not fail the whole tenants:migrate batch.
            // Use: php artisan tenants:repair-spatie {tenantId}
            return;
        }

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
    }

    public function down(): void
    {
        //
    }
};
