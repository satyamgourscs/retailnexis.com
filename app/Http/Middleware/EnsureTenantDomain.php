<?php

namespace App\Http\Middleware;

use App\Models\landlord\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomain = strtolower((string) config('app.central_domain', 'localhost'));
        if ($centralDomain === '') {
            $centralDomain = 'localhost';
        }

        $host = strtolower($request->getHost());

        $tenant = Tenant::whereHas('domains', static function ($q) use ($host) {
            $q->where('domain', $host);
        })->first();

        if ($tenant) {
            return $next($request);
        }

        if ($host === $centralDomain) {
            return $next($request);
        }

        $suffix = '.'.$centralDomain;
        if (! str_ends_with($host, $suffix)) {
            return $next($request);
        }

        $tenantId = substr($host, 0, -strlen($suffix));
        if ($tenantId === '' || str_contains($tenantId, '.')) {
            return $next($request);
        }

        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $tenant->domains()->firstOrCreate([
                'domain' => $host,
            ]);
        }

        return $next($request);
    }
}
