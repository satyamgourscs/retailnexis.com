<?php

/**
 * Laravel front controller for shared hosting (Hostinger / cPanel style).
 *
 * Deploy:
 * - Full project lives OUTSIDE public_html, e.g. /home/USER/laravel_app
 * - Only the CONTENTS of Laravel's `public/` folder go INTO public_html
 * - Copy this file to public_html/index.php (replace the default index.php)
 *
 * Adjust $laravelRoot below if your folder name differs from "laravel_app".
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Absolute path to Laravel root (contains app/, bootstrap/, vendor/, storage/, .env)
$laravelRoot = dirname(__DIR__) . '/laravel_app';

if (! is_dir($laravelRoot)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Laravel root not found. Set \$laravelRoot in public_html/index.php (expected: {$laravelRoot})\n";
    exit(1);
}

$maintenance = $laravelRoot . '/storage/framework/maintenance.php';
if (file_exists($maintenance)) {
    require $maintenance;
}

require $laravelRoot . '/vendor/autoload.php';

$app = require_once $laravelRoot . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();
$kernel->terminate($request, $response);
