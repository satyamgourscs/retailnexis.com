<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Subfolder URL fix (Apache/XAMPP: project URL path + public/index.php)
|--------------------------------------------------------------------------
|
| When the request path includes the project segment, normalize REQUEST_URI
| so routing matches /. Skipped on Nginx with root = /public (SCRIPT_NAME /index.php).
|
*/

if (isset($_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'])) {
    $scriptName = str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']);
    if ($scriptName !== '' && $scriptName[0] !== '/') {
        $scriptName = '/'.$scriptName;
    }
    if ($scriptName !== '' && $scriptName[0] === '/') {
        $scriptDir = dirname($scriptName);
        if ($scriptDir !== '/' && $scriptDir !== '.' && $scriptDir !== '') {
            $requestUri = (string) $_SERVER['REQUEST_URI'];
            $path = (string) (parse_url($requestUri, PHP_URL_PATH) ?? '/');
            $query = parse_url($requestUri, PHP_URL_QUERY);
            $queryString = ($query !== false && $query !== null && $query !== '') ? '?'.$query : '';

            $internalPath = null;

            if (str_starts_with($path, $scriptDir)) {
                $internalPath = substr($path, strlen($scriptDir));
            } else {
                $projectBase = dirname($scriptDir);
                if ($projectBase !== '/' && $projectBase !== '.' && $projectBase !== '' && str_starts_with($path, $projectBase)) {
                    $internalPath = substr($path, strlen($projectBase));
                    if ($internalPath === false) {
                        $internalPath = '';
                    }
                    if ($internalPath === '') {
                        $internalPath = '/';
                    } elseif (($internalPath[0] ?? '') !== '/') {
                        $internalPath = '/'.ltrim((string) $internalPath, '/');
                    }
                    if (str_starts_with($internalPath, '/public')) {
                        $internalPath = substr($internalPath, strlen('/public'));
                        if ($internalPath === '' || $internalPath === false) {
                            $internalPath = '/';
                        } elseif (($internalPath[0] ?? '') !== '/') {
                            $internalPath = '/'.$internalPath;
                        }
                    }
                }
            }

            if ($internalPath !== null) {
                $internalPath = (string) $internalPath;
                if ($internalPath === '' || $internalPath === false) {
                    $internalPath = '/';
                } elseif (($internalPath[0] ?? '') !== '/') {
                    $internalPath = '/'.$internalPath;
                }
                $_SERVER['REQUEST_URI'] = $internalPath.$queryString;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| DocumentRoot = public/ (SCRIPT_NAME /index.php): strip APP_URL path prefix
|--------------------------------------------------------------------------
|
| The block above is skipped when dirname(SCRIPT_NAME) is /. Then requests like
| https://tenant.localhost/saas/login never become /login and Apache may report 500
| or Laravel returns 404. Derive prefixes from APP_URL (/saas/public and /saas).
|
*/

if (isset($_SERVER['REQUEST_URI'])) {
    $appUrl = getenv('APP_URL');
    if (! is_string($appUrl) || $appUrl === '') {
        $envFile = __DIR__.'/../.env';
        if (is_readable($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (! str_starts_with($line, 'APP_URL=')) {
                    continue;
                }
                $appUrl = trim(trim(substr($line, 8)), " \t'\"");
                break;
            }
        }
    }
    if (is_string($appUrl) && $appUrl !== '') {
        $uPath = parse_url($appUrl, PHP_URL_PATH);
        if (is_string($uPath)) {
            $uPath = rtrim($uPath, '/');
            if ($uPath !== '' && $uPath !== '/') {
                $prefixes = [$uPath];
                if (str_ends_with($uPath, '/public')) {
                    $p = substr($uPath, 0, -strlen('/public'));
                    if ($p !== '' && $p !== '/') {
                        $prefixes[] = $p;
                    }
                } else {
                    $prefixes[] = $uPath.'/public';
                }
                $prefixes = array_values(array_unique(array_filter($prefixes)));
                usort($prefixes, static fn ($a, $b) => strlen((string) $b) <=> strlen((string) $a));

                $requestUri = (string) $_SERVER['REQUEST_URI'];
                $reqPath = (string) (parse_url($requestUri, PHP_URL_PATH) ?? '/');
                $query = parse_url($requestUri, PHP_URL_QUERY);
                $queryString = ($query !== false && $query !== null && $query !== '') ? '?'.$query : '';

                foreach ($prefixes as $prefix) {
                    $prefix = (string) $prefix;
                    if ($reqPath === $prefix) {
                        $_SERVER['REQUEST_URI'] = '/'.$queryString;
                        break;
                    }
                    if (str_starts_with($reqPath, $prefix.'/')) {
                        $stripped = substr($reqPath, strlen($prefix));
                        if ($stripped === false || $stripped === '') {
                            $stripped = '/';
                        } elseif (($stripped[0] ?? '') !== '/') {
                            $stripped = '/'.$stripped;
                        }
                        $_SERVER['REQUEST_URI'] = $stripped.$queryString;
                        break;
                    }
                }
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| XAMPP subfolder fallback when APP_URL has no path (e.g. https://tenant.localhost)
|--------------------------------------------------------------------------
|
| If APP_URL is only a scheme+host, the block above does not strip /saas. Tenant HTTPS
| vhosts then keep REQUEST_URI=/saas/login and Laravel has no matching route → errors.
|
*/

if (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = (string) $_SERVER['REQUEST_URI'];
    $reqPath = (string) (parse_url($requestUri, PHP_URL_PATH) ?? '/');
    $query = parse_url($requestUri, PHP_URL_QUERY);
    $queryString = ($query !== false && $query !== null && $query !== '') ? '?'.$query : '';

    foreach (['/saas/public', '/saas'] as $prefix) {
        if ($reqPath === $prefix) {
            $_SERVER['REQUEST_URI'] = '/'.$queryString;

            break;
        }
        if (str_starts_with($reqPath, $prefix.'/')) {
            $stripped = substr($reqPath, strlen($prefix));
            if ($stripped === false || $stripped === '') {
                $stripped = '/';
            } elseif (($stripped[0] ?? '') !== '/') {
                $stripped = '/'.$stripped;
            }
            $_SERVER['REQUEST_URI'] = $stripped.$queryString;

            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();
$kernel->terminate($request, $response);


