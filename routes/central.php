<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central (landlord) host routes — loaded BEFORE routes/web.php and routes/tenant.php
|--------------------------------------------------------------------------
|
| Tenant routes register GET / without a domain constraint. That route matches the
| central host first in the collection and runs PreventAccessFromCentralDomains → 404.
| Domain-scoped central routes must be registered first so localhost/ wins over /.
|
*/

$extraHosts = array_filter([
    strtolower(rtrim((string) config('app.central_domain'), '.')),
], static fn (string $h): bool => $h !== '');
$appUrlHost = parse_url((string) config('app.url'), PHP_URL_HOST);
if (is_string($appUrlHost) && $appUrlHost !== '') {
    $extraHosts[] = strtolower(rtrim($appUrlHost, '.'));
}
$publicUrlHost = parse_url((string) config('app.public_url', ''), PHP_URL_HOST);
if (is_string($publicUrlHost) && $publicUrlHost !== '') {
    $extraHosts[] = strtolower(rtrim($publicUrlHost, '.'));
}
$centralDomains = array_values(array_unique(array_filter(
    array_merge((array) config('tenancy.central_domains', []), $extraHosts),
    static fn ($h): bool => (string) $h !== ''
)));
$expanded = [];
foreach ($centralDomains as $host) {
    $host = strtolower((string) $host);
    if ($host === '') {
        continue;
    }
    $expanded[] = $host;
    $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    if (! $isLocal && ! str_starts_with($host, 'www.')) {
        $expanded[] = 'www.'.$host;
    }
}
$centralDomains = array_values(array_unique($expanded));
$canonicalCentral = strtolower((string) config('app.central_domain'));
if ($canonicalCentral !== '') {
    usort($centralDomains, function ($a, $b) use ($canonicalCentral) {
        $a = strtolower((string) $a);
        $b = strtolower((string) $b);
        if ($a === $canonicalCentral) {
            return -1;
        }
        if ($b === $canonicalCentral) {
            return 1;
        }

        return strcmp($a, $b);
    });
}
$namedCentralHome = false;
foreach ($centralDomains as $domain) {
    $domain = strtolower((string) $domain);
    if ($domain === '') {
        continue;
    }
    Route::domain($domain)->group(function () use (&$namedCentralHome) {
        if (empty(config('app.landlord_db'))) {
            Route::get('/', [\App\Http\Controllers\WebUtilitiesController::class, 'centralInstallerRedirect']);

            return;
        }
        $route = Route::get('/', [\App\Http\Controllers\landlord\LandingPageController::class, 'index']);
        if (! $namedCentralHome) {
            $route->name('central.home');
            $namedCentralHome = true;
        }
    });
}
