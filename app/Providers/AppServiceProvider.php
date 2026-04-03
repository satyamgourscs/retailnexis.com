<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use App\Models\Translation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * URL path prefix before `/public` (e.g. `/saas`) so asset()/url() work on XAMPP subfolders.
     */
    protected function webBasePath(\Illuminate\Http\Request $request): string
    {
        $base = (string) $request->getBasePath();
        if ($base !== '' && str_ends_with($base, '/public')) {
            $base = substr($base, 0, -strlen('/public')) ?: '';
        }
        if ($base !== '') {
            return $base;
        }

        $script = str_replace('\\', '/', (string) $request->server->get('SCRIPT_NAME', ''));
        if ($script !== '' && $script[0] !== '/') {
            $script = '/'.$script;
        }
        if (str_ends_with($script, '/public/index.php')) {
            $base = substr($script, 0, -strlen('/public/index.php'));

            return ($base === '/' || $base === '') ? '' : $base;
        }

        $path = parse_url((string) config('app.url'), PHP_URL_PATH);
        if (is_string($path)) {
            $path = rtrim($path, '/');
            if ($path !== '' && $path !== '/') {
                return $path;
            }
        }

        return '';
    }

    /**
     * Merge resources/lang/{locale}/db.php early so error views never show raw db.* keys.
     */
    protected function registerEarlyDbLangFallbacks(): void
    {
        foreach (array_unique(array_filter([config('app.locale'), config('app.fallback_locale')])) as $locale) {
            $path = resource_path("lang/{$locale}/db.php");
            if (! is_file($path)) {
                continue;
            }
            /** @var mixed $data */
            $data = include $path;
            if (! is_array($data)) {
                continue;
            }
            $lines = [];
            foreach ($data as $key => $value) {
                $lines['db.'.$key] = $value;
            }
            if ($lines !== []) {
                app('translator')->addLines($lines, (string) $locale);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * 
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->app->bind(\App\ViewModels\ISmsModel::class, \App\ViewModels\SmsModel::class);

        if (app()->runningInConsole()) {
            return;
        }

        $this->registerEarlyDbLangFallbacks();

        try {
            $request = request();
            if ($request !== null) {
                $basePath = $this->webBasePath($request);
                $forwardedProto = strtolower((string) $request->header('X-Forwarded-Proto', ''));
                $scheme = ($request->secure()
                    || $forwardedProto === 'https'
                    || $request->server('HTTPS') === 'on'
                    || $request->server('HTTP_X_FORWARDED_SSL') === 'on')
                    ? 'https'
                    : $request->getScheme();
                $host = $request->getHttpHost();
                $root = rtrim($scheme.'://'.$host.$basePath, '/');
                if ($root !== '') {
                    URL::forceRootUrl($root);
                }
                if ($scheme === 'https') {
                    URL::forceScheme('https');
                }
            }
        } catch (\Throwable $e) {
        }

        $translationLogic = function () {
            try {
                if (!DB::connection()->getDatabaseName()) {
                    return;
                }
            } catch (\Exception $e) {
                // Skip logic if DB connection fails
                return;
            }

            try {
                if (isset($_COOKIE['language'])) {
                    App::setLocale($_COOKIE['language']);
                } elseif (Schema::hasTable('languages')) {
                    $language = DB::table('languages')->where('is_default', true)->first();
                    $locale = 'en';
                    if ($language) {
                        $locale = $language->code ?? $language->language ?? 'en';
                    }
                    App::setLocale($locale);
                } else {
                    App::setLocale('en');
                }

                if (Schema::hasTable('translations')) {
                    $currentLocale = App::getLocale();

                    $translations = Cache::rememberForever("translations_{$currentLocale}", function () use ($currentLocale) {
                        return \App\Models\Translation::getTrnaslactionsByLocale($currentLocale);
                    });

                    if (!empty($translations)) {
                        app('translator')->addLines($translations, $currentLocale);
                    }
                }
            } catch (\Exception $e) {
                // Optional: log the error
                // Log::error($e->getMessage());
            }
        };

        if (config('database.connections.retailnexis_landlord')) {
            ///new code for superadmin//
            if (!app()->bound('tenancy')) {
                $landlordConn = (string) config('tenancy.database.central_connection', 'retailnexis_landlord');
                $locale = null;
                try {
                    if (Schema::connection($landlordConn)->hasTable('languages')) {
                        $default_language = DB::connection($landlordConn)->table('languages')->where('is_default', true)->first();
                        $locale = $default_language->code ?? 'en';
                    } else {
                        $locale = 'en';
                    }
                } catch (\Throwable $e) {
                    $locale = 'en';
                }

                // Finally, set the app locale
                App::setLocale($locale);

                // Check if language file exists
                $langFile = resource_path("lang/{$locale}.php");
                if (!file_exists($langFile)) {
                    $langFile = resource_path("lang/master.php");
                }

                $transData = include $langFile; // loads the array
                $translations = [];
                foreach ($transData as $group => $items) {
                    foreach ($items as $key => $value) {
                        $translations["{$group}.{$key}"] = $value;
                    }
                }
                // Merge translations into Laravel's translator
                app('translator')->addLines($translations, $locale);
            }
            ///new code for superadmin//

            Event::listen(TenancyBootstrapped::class, function () use ($translationLogic) {
                $translationLogic();
            });

        } else {
            $translationLogic();
        }
    }
}
