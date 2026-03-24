<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Default post-auth redirect path.
     *
     * Some auth controllers (e.g. ConfirmPasswordController) reference
     * RouteServiceProvider::HOME, but this project didn't define it.
     */
    public const HOME = '/';

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        /*parent::boot();*/

        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        // Register once: per-domain registration duplicates route names and breaks `php artisan route:cache`.
        Route::middleware('web')
            ->namespace('App\Http\Controllers')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        // Register once: naming the same routes per central domain breaks `php artisan route:cache`.
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Web routes are registered per-domain (see mapWebRoutes). If config:cache was built with a
     * local APP_URL, retailnexis.com may be missing from tenancy.central_domains → 404 on production.
     * Merge the current request host (and APP_PUBLIC_URL) so central routes register for the real domain.
     */
    protected function centralDomains(): array
    {
        $domains = config('tenancy.central_domains');
        if (! is_array($domains)) {
            $domains = [];
        }

        $hostsToAdd = [];

        $publicUrl = config('app.public_url');
        if (is_string($publicUrl) && $publicUrl !== '') {
            $h = parse_url($publicUrl, PHP_URL_HOST);
            if (is_string($h) && $h !== '') {
                $hostsToAdd[] = strtolower(rtrim($h, '.'));
            }
        }

        if (! $this->app->runningInConsole() && $this->app->bound('request')) {
            /** @var Request $request */
            $request = $this->app->make('request');
            $forwarded = $request->headers->get('X-Forwarded-Host');
            $host = $forwarded ? trim(explode(',', $forwarded)[0]) : $request->getHost();
            if (is_string($host) && $host !== '') {
                $hostsToAdd[] = strtolower(rtrim($host, '.'));
            }
        }

        $have = array_map('strtolower', $domains);
        foreach (array_unique($hostsToAdd) as $h) {
            if ($h === '' || in_array($h, ['localhost', '127.0.0.1', '::1'], true)) {
                continue;
            }
            if (! in_array($h, $have, true)) {
                $domains[] = $h;
                $have[] = $h;
                if (! str_starts_with($h, 'www.')) {
                    $www = 'www.'.$h;
                    if (! in_array($www, $have, true)) {
                        $domains[] = $www;
                        $have[] = $www;
                    }
                }
            }
        }

        $domains = array_values(array_unique(array_filter($domains)));
        config(['tenancy.central_domains' => $domains]);

        return $domains;
    }
}
