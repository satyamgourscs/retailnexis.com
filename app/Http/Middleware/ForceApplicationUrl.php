<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceApplicationUrl
{
    /**
     * After TrustProxies: align config('app.url') and URL generator with the real public domain.
     * Fixes redirects and url() pointing at localhost / 127.0.0.1 when .env was copied from local.
     */
    public function handle(Request $request, Closure $next)
    {
        $public = config('app.public_url');
        if (is_string($public) && $public !== '') {
            $root = rtrim($public, '/');
            config(['app.url' => $root]);
            URL::forceRootUrl($root);
            URL::forceScheme(str_starts_with($root, 'https') ? 'https' : 'http');
            $this->sanitizeSessionPreviousUrlIfProduction($request);

            return $next($request);
        }

        $appUrl = (string) config('app.url', '');
        $looksLocal = $appUrl === ''
            || str_contains($appUrl, '127.0.0.1')
            || str_contains($appUrl, 'localhost')
            || str_contains($appUrl, '::1');

        if (! $looksLocal) {
            $this->sanitizeSessionPreviousUrlIfProduction($request);

            return $next($request);
        }

        $host = $request->getHost();
        if (in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            $fwd = $request->headers->get('X-Forwarded-Host');
            if ($fwd) {
                $host = trim(explode(',', $fwd)[0]);
            }
        }

        if (! in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            $scheme = $request->getScheme();
            $root = $scheme.'://'.$host;
            config(['app.url' => $root]);
            URL::forceRootUrl($root);
            URL::forceScheme($request->secure() ? 'https' : 'http');
        }

        $this->sanitizeSessionPreviousUrlIfProduction($request);

        return $next($request);
    }

    /**
     * Avoid redirect()->back() sending users to localhost when the session still has a dev _previous.url.
     */
    private function sanitizeSessionPreviousUrlIfProduction(Request $request): void
    {
        if (! $request->hasSession() || in_array($request->getHost(), ['127.0.0.1', 'localhost', '::1'], true)) {
            return;
        }

        $prev = $request->session()->get('_previous.url');
        if (! is_string($prev) || $prev === '') {
            return;
        }

        if (! str_contains($prev, '127.0.0.1') && ! str_contains($prev, 'localhost') && ! str_contains($prev, '::1')) {
            return;
        }

        $path = parse_url($prev, PHP_URL_PATH);
        $query = parse_url($prev, PHP_URL_QUERY);
        $fragment = parse_url($prev, PHP_URL_FRAGMENT);
        if ($path === null || $path === false || $path === '') {
            $path = '/';
        }
        $root = rtrim((string) config('app.url'), '/');
        $rebuilt = $root.$path;
        if ($query) {
            $rebuilt .= '?'.$query;
        }
        if ($fragment) {
            $rebuilt .= '#'.$fragment;
        }
        $request->session()->put('_previous.url', $rebuilt);
    }
}
