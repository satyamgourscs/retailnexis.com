<?php

declare(strict_types=1);

namespace App\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

/**
 * Extends Stancl resolution so tenants are found when:
 * - FQDN matches domains.domain (default behaviour), or
 * - Host is {tenant_id}.{central} but DB row used another central host (e.g. 127.0.0.1 vs localhost).
 */
class CustomDomainTenantResolver extends DomainTenantResolver
{
    public function resolveWithoutCache(...$args): Tenant
    {
        $domain = (string) ($args[0] ?? '');

        $tenant = config('tenancy.tenant_model')::query()
            ->whereHas('domains', function (Builder $query) use ($domain) {
                $query->where('domain', $domain);
            })
            ->with('domains')
            ->first();

        if ($tenant) {
            $this->setCurrentDomain($tenant, $domain);

            return $tenant;
        }

        $tenant = $this->resolveByTenantSlugSuffix($domain);
        if ($tenant) {
            $this->setCurrentDomainFlexible($tenant, $domain);

            return $tenant;
        }

        throw new TenantCouldNotBeIdentifiedOnDomainException($domain);
    }

    /**
     * If hostname ends with a configured central domain, treat the leading label as tenant id.
     */
    protected function resolveByTenantSlugSuffix(string $hostname): ?Tenant
    {
        $hostname = strtolower(rtrim($hostname, '.'));
        $slug = $this->tenantSlugFromHost($hostname);
        if ($slug === null) {
            return null;
        }

        /** @var class-string<Tenant> $model */
        $model = config('tenancy.tenant_model');

        // Any FQDN stored as "{slug}.*" (covers localhost vs 127.0.0.1 and other central hosts).
        $tenant = $model::query()
            ->whereHas('domains', function (Builder $q) use ($slug) {
                $q->where('domain', 'like', $slug.'.%');
            })
            ->with('domains')
            ->first();

        if ($tenant instanceof Tenant) {
            return $tenant;
        }

        $tenant = $model::query()
            ->where('id', $slug)
            ->with('domains')
            ->first();

        if (! $tenant instanceof Tenant) {
            return null;
        }

        if ($tenant->domains->isEmpty()) {
            // Tenant row exists but domain row never created / failed — bind this host so routing works.
            $tenant->domains()->create(['domain' => $hostname]);

            return $tenant->fresh(['domains']);
        }

        return $tenant;
    }

    protected function tenantSlugFromHost(string $hostname): ?string
    {
        $hostnameLower = strtolower($hostname);
        $central = (array) config('tenancy.central_domains', []);
        $bestCentral = null;
        $bestLen = 0;

        foreach ($central as $entry) {
            $entry = strtolower(rtrim(trim((string) $entry), '.'));
            if ($entry === '') {
                continue;
            }
            $suffix = '.'.$entry;
            if (! str_ends_with($hostnameLower, $suffix)) {
                continue;
            }
            if (strlen($entry) > $bestLen) {
                $bestLen = strlen($entry);
                $bestCentral = $entry;
            }
        }

        if ($bestCentral === null && str_ends_with($hostnameLower, '.localhost')) {
            $bestCentral = 'localhost';
        }

        if ($bestCentral === null) {
            return null;
        }

        $suffix = '.'.(string) $bestCentral;
        $prefix = substr($hostnameLower, 0, -strlen($suffix));
        if ($prefix === '' || str_contains($prefix, '.')) {
            return null;
        }

        return $prefix;
    }

    protected function setCurrentDomainFlexible(Tenant $tenant, string $requestHost): void
    {
        $tenant->unsetRelation('domains');
        $tenant->load('domains');

        $domainRow = $tenant->domains->firstWhere('domain', $requestHost);
        if ($domainRow !== null) {
            static::$currentDomain = $domainRow;

            return;
        }

        $head = strstr($requestHost, '.', true);
        $sub = ($head !== false ? strtolower($head) : '');
        $domainRow = $tenant->domains->first(function ($d) use ($sub) {
            $fqdn = (string) $d->domain;
            $leftPart = strstr($fqdn, '.', true);

            $left = ($leftPart !== false ? strtolower($leftPart) : strtolower($fqdn));

            return $left === $sub;
        });

        static::$currentDomain = $domainRow ?? $tenant->domains->first();
    }
}
