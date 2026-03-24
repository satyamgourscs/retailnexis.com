<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\MailSetting;
use App\Models\landlord\GeneralSetting;
use DB;
use Mail;

class SettingController extends Controller
{
    use \App\Traits\CacheForget;
    use \App\Traits\TenantInfo;
    use \App\Traits\MailInfo;

    public function superadminGeneralSetting()
    {
        $lims_general_setting_data = GeneralSetting::latest()->first();
        $payment_gateways = DB::table('external_services')->where('type', 'payment')->get();
        return view('landlord.general_setting', compact('lims_general_setting_data', 'payment_gateways'));
    }

    public function superadminGeneralSettingStore(Request $request)
    {
        if(!config('app.user_verified'))
            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));

        $this->validate($request, [
            'site_logo' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
            'og_image' => 'image|mimes:jpg,jpeg,png|max:100000',
        ]);

        $data = $request->except('site_logo');
        if (isset($data['is_rtl']))
            $data['is_rtl'] = true;
        else
            $data['is_rtl'] = false;

        if(!isset($data['disable_frontend_signup'])){
            $data['disable_frontend_signup'] = 0;
        }

        if(!isset($data['disable_tenant_support_tickets'])){
            $data['disable_tenant_support_tickets'] = 0;
        }

        $general_setting = GeneralSetting::latest()->first();
        $general_setting->id = 1;
        $general_setting->site_title = $data['site_title'];
        $general_setting->is_rtl = $data['is_rtl'];
        $general_setting->phone = $data['phone'];
        $general_setting->email = $data['email'];
        $general_setting->free_trial_limit = $data['free_trial_limit'];
        $general_setting->date_format = $data['date_format'];
        $general_setting->dedicated_ip = $data['dedicated_ip'];
        $general_setting->currency = $data['currency'];
        $general_setting->disable_frontend_signup = $data['disable_frontend_signup'];
        $general_setting->disable_tenant_support_tickets = $data['disable_tenant_support_tickets'];
        $general_setting->theme_color = $data['theme_color'];
        $general_setting->developed_by = $data['developed_by'];
        $logo = $request->site_logo;
        $general_setting->meta_title = $data['meta_title'];
        $general_setting->meta_description = $data['meta_description'];
        $general_setting->og_title = $data['og_title'];
        $general_setting->og_description = $data['og_description'];
        $general_setting->chat_script = $data['chat_script'];
        $general_setting->ga_script = $data['ga_script'];
        $general_setting->fb_pixel_script = $data['fb_pixel_script'];
        $og_image = $request->og_image;
        if ($logo) {
            $this->fileDelete('landlord/images/logo/', $general_setting->site_logo);

            $ext = pathinfo($logo->getClientOriginalName(), PATHINFO_EXTENSION);
            $logoName = date("Ymdhis") . '.' . $ext;
            $logo->move(public_path('landlord/images/logo'), $logoName);
            $general_setting->site_logo = $logoName;
        }
        if ($og_image) {
            $this->fileDelete('landlord/images/og-image/', $general_setting->og_image);

            $ext = pathinfo($og_image->getClientOriginalName(), PATHINFO_EXTENSION);
            $og_image_name = date("Ymdhis") . '.' . $ext;
            $og_image->move(public_path('landlord/images/og-image/'), $og_image_name);
            $general_setting->og_image = $og_image_name;
        }
        $this->cacheForget('general_setting');
        $general_setting->save();

        /////////////////////////Start payment gateway saved in landlord external_services table////////////////////
        // Fetch all payment gateways from the database
        $gateways = DB::table('external_services')->where('type', 'payment')->get();

        // Get inputs
        $pgs = $request->input('pg_name', []); // Payment gateway names
        $actives = $request->input('active', []); // Active status for each gateway

        foreach ($pgs as $index => $pg) {
            $gateway = $gateways->where('name', $pg)->first();

            if (!$gateway) {
                continue; // Skip if gateway not found
            }

            // Update the `details` field
            $lines = explode(';', $gateway->details);
            $keys = explode(',', $lines[0]);
            $vals = [];
            foreach ($keys as $key) {
                $para = $pg . '_' . str_replace(' ', '_', $key);
                $val = $request->$para ?? ''; // Default to empty string if null
                array_push($vals, $val);
            }
            $lines[1] = implode(',', $vals);
            $details = $lines[0] . ';' . $lines[1];

            // Update the gateway in the database
            DB::table('external_services')
                ->where('name', $pg)
                ->update([
                    'details' => $details,
                    'active' => $actives[$index] ?? 1,
                ]);
        }
        /////////////////////////End payment gateway saved in external_services table////////////////////

        return redirect()->route('superadminGeneralSetting')->with('message', __('db.Data updated successfully'));
    }

    public function superadminMailSetting()
    {
        $mail_setting_data = MailSetting::latest()->first();
        return view('landlord.mail_setting', compact('mail_setting_data'));
    }

    public function superadminMailSettingStore(Request $request)
    {
        if(!config('app.user_verified'))
            return redirect()->back()->with('not_permitted', __('db.This feature is disable for demo!'));

        try {
            $data = $request->validate([
                'driver' => ['required', 'string', 'max:255'],
                'host' => ['required', 'string', 'max:255'],
                'port' => ['required', 'integer', 'min:1', 'max:65535'],
                'from_address' => ['required', 'string', 'max:255'],
                'from_name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string'],
                'encryption' => ['required', 'string', 'max:50'],
            ]);

            $mail_setting = MailSetting::latest()->first();
            if(!$mail_setting) {
                $mail_setting = new MailSetting;
            }

            $mail_setting->driver = $data['driver'];
            $mail_setting->host = $data['host'];
            $mail_setting->port = $data['port'];
            $mail_setting->from_address = $data['from_address'];
            $mail_setting->from_name = $data['from_name'];
            $mail_setting->username = $data['username'];
            $mail_setting->password = trim($data['password']);
            $mail_setting->encryption = $data['encryption'];

            $mail_setting->save();

            $this->setMailInfo($mail_setting);
            // Send test mail to from_address
            Mail::raw(__('db.This is a test mail to confirm your SMTP settings are working.'), function ($message) use ($mail_setting) {
                $message->to($mail_setting->from_address)
                        ->subject(__('db.Test Mail'));
            });

            return redirect()->route('superadminMailSetting')->with(
                'message',
                __('db.data_updated_mail_sent') . ' ' . $mail_setting->from_address
            );
        } catch (\Exception $e) {
            // Fail gracefully (avoid 500 "server snapped" pages)
            return redirect()->route('superadminMailSetting')->with(
                'not_permitted',
                __('db.data_updated_mail_fail') . ' ' . mb_substr($e->getMessage(), 0, 2000)
            );
        }
    }

}
