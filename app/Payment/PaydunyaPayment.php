<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
use Paydunya;
use App\Models\landlord\GeneralSetting;
use App\Models\landlord\Package;
use DB;

class PaydunyaPayment implements PaybleContract
{
    public function pay($request, $otherRequest)
    {
        $general_setting = GeneralSetting::latest()->first();
        $pg = DB::table('external_services')->where('name','paydunya')->where('type','payment')->first();
        $lines = explode(';',$pg->details);
        $vals = explode(',', $lines[1]);

        $package = Package::select('name')->find($request->package_id);
        //setting up Paydunya credentials
        Paydunya\Setup::setMasterKey($vals[0]);
        Paydunya\Setup::setPublicKey($vals[1]);
        Paydunya\Setup::setPrivateKey($vals[2]);
        Paydunya\Setup::setToken($vals[3]);
        //Paydunya\Setup::setMode("test");

        Paydunya\Checkout\Store::setName($general_setting->site_title); // Only the name is required
        Paydunya\Checkout\Store::setPhoneNumber($general_setting->phone);
        $base = rtrim((string) config('app.url'), '/');
        Paydunya\Checkout\Store::setWebsiteUrl($base);
        Paydunya\Checkout\Store::setLogoUrl($base.'/landlord/images/logo/'.$general_setting->site_logo);

        Paydunya\Checkout\Store::setCallbackUrl($base);
        Paydunya\Checkout\Store::setReturnUrl($base.'/payment_success');
        Paydunya\Checkout\Store::setCancelUrl($base);

        $invoice = new Paydunya\Checkout\CheckoutInvoice();
        $invoice->addItem($package->name, 1, $request->price, $request->price, $package->subscription_type." subscription.");
        $invoice->setTotalAmount($request->price);
        // Addition of several payment methods at a time
        //$invoice->addChannels(['card', 'jonijoni-senegal', 'orange-money-senegal']);
        if($invoice->create()) {
            return \Redirect::to($invoice->getInvoiceUrl());
        }else{
            echo $invoice->response_text;
        }
    }

    public function cancel()
    {
        return redirect('/');
    }
}

?>
