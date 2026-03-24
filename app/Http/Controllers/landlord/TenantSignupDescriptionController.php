<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\TenantSignupDescription;
use App\Models\Language;

class TenantSignupDescriptionController extends Controller
{
    use \App\Traits\CacheForget;

    public function index()
    {
        $tenant_signup_description = TenantSignupDescription::where('lang_id', config('lang_id'))->first();
        $language_all = Language::where('is_active', true)->get();
        return view('landlord.tenant_signup_description', compact('tenant_signup_description', 'language_all'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $tenant_signup_description = TenantSignupDescription::where('lang_id', $request->language_id)->first();
        if(!$tenant_signup_description)
            $tenant_signup_description = new TenantSignupDescription;
        $tenant_signup_description->heading = $request->heading;
        $tenant_signup_description->sub_heading = $request->sub_heading;
        $tenant_signup_description->lang_id = $request->language_id;
        $tenant_signup_description->save();
        $this->cacheForget('tenant_signup_descriptions');
        return redirect()->back()->with('message', 'Data updated successfully');
    }
}
