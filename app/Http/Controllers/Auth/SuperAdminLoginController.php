<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\User;

class SuperAdminLoginController extends Controller
{
    use AuthenticatesUsers;

    public function login()
    {
        if(isset($_COOKIE['language']))
            \App::setLocale($_COOKIE['language']);
        else
            \App::setLocale('en');
        //getting theme
        if(isset($_COOKIE['theme']))
            $theme = $_COOKIE['theme'];
        else
            $theme = 'light';
        $general_setting = \App\Models\landlord\GeneralSetting::latest()->first();
        return view('landlord.login', compact('theme', 'general_setting'));
    }

    public function store(Request $request)
    {
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
