<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\Models\landlord\GeneralSetting;
use App\Support\LandlordConnection;
use Illuminate\Support\Facades\Log;

class SuperAdminLoginController extends Controller
{
    use AuthenticatesUsers;

    public function login()
    {
        LandlordConnection::ensureSaleprosaasLandlordIsDefault();

        if(isset($_COOKIE['language']))
            \App::setLocale($_COOKIE['language']);
        else
            \App::setLocale('en');
        //getting theme
        if(isset($_COOKIE['theme']))
            $theme = $_COOKIE['theme'];
        else
            $theme = 'light';
        try {
            $general_setting = GeneralSetting::latest()->first();
        } catch (\Throwable $e) {
            Log::error('Superadmin login page: landlord database unavailable', [
                'message' => $e->getMessage(),
            ]);
            $general_setting = null;
        }
        return view('landlord.login', compact('theme', 'general_setting'));
    }

    public function store(Request $request)
    {
        LandlordConnection::ensureSaleprosaasLandlordIsDefault();

        // Form field is `name` (label "UserName") but many users enter email.
        // Auth must query the correct column: `name` OR `email`, not both at once.
        $login = trim((string) $request->input('name', ''));
        $password = $request->input('password');

        if ($login === '' || $password === null || $password === '') {
            return redirect()->back()->with('not_permitted', __('db.Invalid username or password'));
        }

        $credentials = ['password' => $password];
        if (str_contains($login, '@')) {
            $credentials['email'] = $login;
        } else {
            $credentials['name'] = $login;
        }

        if (auth()->attempt($credentials)) {
            // Use a same-host path redirect to avoid cross-domain named-route collisions
            // (e.g. localhost -> www.localhost) that can break session/CSRF.
            return redirect('/superadmin/dashboard');
        }

        return redirect()->back()->with('not_permitted', __('db.Invalid username or password'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect('/');
    }
}
