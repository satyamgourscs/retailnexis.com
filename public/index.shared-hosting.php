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

// Resolve Laravel root: supports both
// - /home/USER/public_html + /home/USER/laravel_app
// - /home/USER/domains/DOMAIN/public_html + /home/USER/laravel_app (Hostinger)
$folder = getenv('LARAVEL_FOLDER') ?: 'laravel_app';
$candidates = [
    dirname(__DIR__) . '/' . $folder,
    dirname(__DIR__, 3) . '/' . $folder,
    dirname(__DIR__, 2) . '/' . $folder,
];

$laravelRoot = null;
foreach ($candidates as $path) {
    if (is_dir($path) && is_file($path . '/vendor/autoload.php')) {
        $laravelRoot = $path;
        break;
    }
}

if ($laravelRoot === null) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Laravel root not found. Tried:\n - " . implode("\n - ", $candidates) . "\n";
    echo "Set LARAVEL_FOLDER if the project folder is not \"laravel_app\", or edit \$candidates in index.php.\n";
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
