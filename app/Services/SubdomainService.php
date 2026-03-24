<?php

namespace App\Services;

use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubdomainService
{
    public function sanitizeSubdomain(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        // Best-effort transliteration to ASCII, then normalize.
        $value = Str::ascii($value);
        $value = strtolower($value);

        // Allow only a-z, 0-9 and hyphen.
        $value = preg_replace('/[^a-z0-9-]+/', '', $value);
        $value = preg_replace('/-+/', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    public function generateUniqueSubdomain(string $companyName, ?string $ignoreTenantId = null): string
    {
        $base = $this->sanitizeSubdomain($companyName);
        if ($base === '') {
            $base = 'client';
        }

        $centralDomain = $this->getCentralDomain();

        if (! $this->subdomainTaken($base, $centralDomain, $ignoreTenantId)) {
            return $base;
        }

        // Required rule: base exists => base2, base3, ...
        $suffix = 2;
        do {
            $candidate = $base . $suffix;
            $suffix++;
        } while ($this->subdomainTaken($candidate, $centralDomain, $ignoreTenantId));

        return $candidate;
    }

    public function getCentralDomain(): string
    {
        $central = trim((string) env('CENTRAL_DOMAIN'));
        if ($central === '') {
            return '';
        }

        // Normalize: strip scheme/path/port and keep host only.
        $central = preg_replace('#^https?://#i', '', $central);
        $central = preg_replace('#/.*$#', '', $central);
        $central = strtolower($central);
        $central = preg_replace('/:\d+$/', '', $central);

        return $central;
    }

    private function subdomainTaken(string $subdomain, string $centralDomain, ?string $ignoreTenantId): bool
    {
        if ($ignoreTenantId !== null && $subdomain === $ignoreTenantId) {
            return false;
        }

        if (Tenant::query()->where('id', $subdomain)->exists()) {
            return true;
        }

        if ($centralDomain === '') {
            return false;
        }

        $fullDomain = $subdomain . '.' . $centralDomain;

        return DB::table('domains')->where('domain', $fullDomain)->exists();
    }
}

