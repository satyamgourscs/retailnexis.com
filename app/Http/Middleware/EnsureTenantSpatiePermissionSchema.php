<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * If tenant migrations were interrupted, `permissions` + `roles` may exist but
 * `role_has_permissions` is missing. Create the pivot on the fly so the app can run.
 *
 * If base tables are missing, this does nothing (admin must migrate-fresh or recreate client).
 */
class EnsureTenantSpatiePermissionSchema
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! tenancy()->initialized) {
            return $next($request);
        }

        if (Schema::hasTable('role_has_permissions')) {
            return $next($request);
        }

        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return $next($request);
        }

        try {
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
        } catch (\Throwable $e) {
            // Race: another request created the table, or FK mismatch
            if (! Schema::hasTable('role_has_permissions')) {
                report($e);
            }
        }

        return $next($request);
    }
}
