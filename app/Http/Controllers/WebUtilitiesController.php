<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class WebUtilitiesController extends Controller
{
    public function paymentCancel(): string
    {
        return 'payment_cancel';
    }

    public function failUrl(): string
    {
        return 'fail_url';
    }

    public function clearCaches(): never
    {
        Artisan::call('optimize:clear');
        cache()->forget('hero');
        cache()->forget('module_descriptions');
        cache()->forget('faq_descriptions');
        cache()->forget('tenant_signup_descriptions');
        dd('cleared');
    }

    public function centralInstallerRedirect(): RedirectResponse
    {
        return redirect()->route('saas-install-step-1');
    }

    public function superadminHomeRedirect(): RedirectResponse
    {
        return redirect()->route('superadmin.dashboard');
    }
}
