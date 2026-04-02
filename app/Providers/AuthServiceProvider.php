<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // ❌ old broken mapping removed
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // installer redirect code (disabled)
        // $installDir = base_path('install');
        // if (is_dir($installDir)) {
        //     redirect()->to('saas/install/step-1');
        //     exit();
        // }
    }
}