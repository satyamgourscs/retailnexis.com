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

            return $next($request);
        }

        $appUrl = (string) config('app.url', '');
        $looksLocal = $appUrl === ''
            || str_contains($appUrl, '127.0.0.1')
            || str_contains($appUrl, 'localhost')
            || str_contains($appUrl, '::1');

        if (! $looksLocal) {
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

        return $next($request);
    }
}
