<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\View;
use DB;
use App\Support\LandlordConnection;
use Illuminate\Support\Facades\Log;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        LandlordConnection::ensureSaleprosaasLandlordIsDefault();

        if (! Auth::check()) {
            return redirect('superadmin-login');
        }

        try {
            $default_language = DB::table('languages')->where('is_default', true)->first();

            if (isset($_COOKIE['theme'])) {
                View::share('theme', $_COOKIE['theme']);
            } else {
                View::share('theme', 'light');
            }

            $general_setting = DB::table('general_settings')->latest()->first();
            $lang_id = $default_language ? $default_language->id : 1;
            View::share('general_setting', $general_setting);
            View::share('lang_id', $lang_id);
            if ($general_setting) {
                config(['date_format' => $general_setting->date_format, 'lang_id' => $lang_id]);
            }
        } catch (\Throwable $e) {
            Log::error('SuperAdminAuth: landlord database query failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            Auth::logout();

            return redirect('superadmin-login')->with(
                'not_permitted',
                'Superadmin data could not be loaded. Check LANDLORD_DB, DB_* credentials, and MySQL privileges.'
            );
        }

        return $next($request);
    }
}

