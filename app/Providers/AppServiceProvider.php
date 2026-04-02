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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    /**
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
