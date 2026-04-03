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
        $host = strtolower($request->getHost());

        $tenant = Tenant::whereHas('domains', static function ($q) use ($host) {
            $q->where('domain', $host);
        })->first();

        if ($tenant) {
            return $next($request);
        }

        // Exact central hosts only (never treat tenant FQDNs as central).
        $centralHosts = array_map(
            static fn (string $h): string => strtolower(rtrim($h, '.')),
            (array) config('tenancy.central_domains', [])
        );
        $explicit = strtolower(rtrim(trim((string) env('CENTRAL_DOMAIN', '')), '.'));
        if ($explicit !== '' && ! in_array($explicit, $centralHosts, true)) {
            $centralHosts[] = $explicit;
        }

        if (in_array($host, $centralHosts, true)) {
            return $next($request);
        }

        foreach ($centralHosts as $apex) {
            if ($apex === '' || $host === $apex) {
                continue;
            }
            $suffix = '.'.$apex;
            if (! str_ends_with($host, $suffix)) {
                continue;
            }
            $tenantId = substr($host, 0, -strlen($suffix));
            if ($tenantId === '' || str_contains($tenantId, '.')) {
                continue;
            }
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                $tenant->domains()->firstOrCreate([
                    'domain' => $host,
                ]);
            }
            break;
        }

        return $next($request);
    }
}
