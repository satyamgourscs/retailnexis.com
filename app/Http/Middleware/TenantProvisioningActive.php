<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TenantProvisioningActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! tenancy()->initialized) {
            return $next($request);
        }

        $tenant = tenant();
        if (! $tenant) {
            return $next($request);
        }

        // Backwards compatible: if the column doesn't exist yet, treat as active.
        $status = $tenant->provisioning_status ?? null;
        if ($status === null) {
            return $next($request);
        }

        $status = strtolower((string) $status);
        if ($status === 'active') {
            return $next($request);
        }

        // If the admin set provisioning_status to pending by mistake, but the tenant DB
        // is already initialized (core tables exist), do not block logins.
        if (in_array($status, ['pending', 'provisioning'], true)) {
            try {
                if (Schema::hasTable('general_settings')) {
                    // Best-effort auto-heal so future requests don't keep hitting this branch.
                    try {
                        $tenant->update([
                            'provisioning_status' => 'active',
                            'provisioning_error' => null,
                            'provisioning_completed_at' => $tenant->provisioning_completed_at ?? now(),
                        ]);
                    } catch (\Throwable $e) {
                        // ignore
                    }

                    return $next($request);
                }
            } catch (\Throwable $e) {
                // If schema check itself fails, fall through to block.
            }
        }

        $message = 'Client is being provisioned. Please try again in a few minutes.';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['message' => $message, 'status' => $status], 503);
        }

        // Keep it simple; this should never crash Stancl central/superadmin routes.
        abort(503, $message);
    }
}

