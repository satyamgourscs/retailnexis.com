<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Models\landlord\GeneralSetting;
use App\Models\landlord\Tenant;
use App\Models\landlord\Page;
use DB;
use Cache;
use App\Models\landlord\Package;
use App\Models\landlord\TenantPayment;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    use \App\Traits\TenantInfo;

    public function tenantCheckout(Request $request)
    {
        if (!config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }
        // Check if email is verified
        if (!session()->has('verified_email') || session('verified_email') !== $request->email) {
            return redirect()->back()->with('not_permitted', 'Please verify your email before proceeding!');
        }
        Session::forget('verified_email');

        $package = Package::select('is_free_trial', 'monthly_fee', 'yearly_fee')->find($request->package_id);
        $payment_gateways = DB::table('external_services')->where('type', 'payment')->where('active', true)->get();
        $search = 'Terms';
        $terms_and_condition_page = Page::select('slug')->where('title', 'LIKE', "%{$search}%")->first();
        if ($package->is_free_trial || $package->monthly_fee == 0 || $package->yearly_fee == 0) {
            $this->createTenant($request);
            return \Redirect::to('https://' . $request->tenant . '.' . env('CENTRAL_DOMAIN'));
        } else
            return view('payment.tenant_checkout', compact('request', 'payment_gateways', 'terms_and_condition_page'));
    }

    public function paymentProcees(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Check your all request validation, filter or apply other condition what you need
        |--------------------------------------------------------------------------
        */
        return redirect(route("payment.pay.page", $request->payment_type), 307);
    }

    public function paymentPayPage($paymentMethod, Request $request)
    {
        if (gettype($request->requestData) === 'string') {
            //during bkash, when it redirects back the bkash page after getting any error. Because this time request was empty
            $requestData = $request->requestData;
            $request = json_decode($request->requestData);
        } else {
            $requestData = json_encode($request->all());
        }

        $totalAmount = $request->price;
        switch ($paymentMethod) {
            case 'stripe':
                $paymentMethodName = "Stripe";
                $pg = DB::table('external_services')->where('name','stripe')->where('type','payment')->first();
                $lines = explode(';',$pg->details);
                $vals = explode(',', $lines[1]);
                $stripe_public_key = $vals[0];
                return view('payment.payment_page.stripe', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount', 'stripe_public_key'));
            case 'paypal':
                $paymentMethodName = "Paypal";
                $pg = DB::table('external_services')->where('name','paypal')->where('type','payment')->first();
                $lines = explode(';',$pg->details);
                $vals = explode(',', $lines[1]);
                $paypal_client_id = $vals[0];
                $currency = GeneralSetting::select('paypal_client_id', 'currency')->latest()->first()->currency;
                return view('payment.payment_page.paypal', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount', 'paypal_client_id', 'currency'));
            case 'razorpay':
                $paymentMethodName = "Razorpay";
                $pg = DB::table('external_services')->where('name','razorpay')->where('type','payment')->first();
                $lines = explode(';',$pg->details);
                $vals = explode(',', $lines[1]);
                $razorpay_key = $vals[0];
                return view('payment.payment_page.razorpay', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount', 'razorpay_key'));
            case 'paystack':
                $paymentMethodName = "Paystack";
                $pg = DB::table('external_services')->where('name','paystack')->where('type','payment')->first();
                $lines = explode(';',$pg->details);
                $vals = explode(',', $lines[1]);
                $paystack_secret_key = $vals[1];
                return view('payment.payment_page.paystack', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount', 'paystack_secret_key'));
            case 'paydunya':
                $paymentMethodName = "Paydunya";
                return view('payment.payment_page.paydunya', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount'));
            case 'bkash':
                $paymentMethodName = "bKash";
                return view('payment.payment_page.bkash', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount'));
            case 'sslcommerz':
                $paymentMethodName = "SSL Commerz";
                return view('payment.payment_page.ssl_commerz', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount'));
            case 'pesapal':
                $paymentMethodName = "Pesapal";
                return view('payment.payment_page.pesapal', compact('paymentMethodName', 'paymentMethod', 'requestData', 'totalAmount'));
            default:
                break;
        }
        return;
    }

    public function paymentPayConfirm($paymentMethod, Request $request, PaymentService $paymentService)
    {
        $requestData = json_decode(str_replace('&quot;', '"', $request->requestData));
        if ($paymentMethod == 'stripe' || $paymentMethod == 'paypal' || $paymentMethod == 'razorpay') {
            $data = json_decode($request->requestData);
            if ($data->tenant) {
                $data->payment_method = $paymentMethod;
                $this->createTenant($data);
            } else if ($data->renewal) {
                $tenant = Tenant::find($data->id);
                $tenant->update(['expiry_date' => $data->expiry_date, 'package_id' => $data->package_id, 'subscription_type' => $data->subscription_type]);
                TenantPayment::create(['tenant_id' => $data->id, 'amount' => $data->price, 'paid_by' => $paymentMethod]);

                $this->changePermission(
                    $tenant,
                    $data->abandoned_permission_ids,
                    $data->permission_ids,
                    $data->package_id,
                    $data->modules,
                    $data->expiry_date,
                    $data->subscription_type
                );
            }
            $payment = $paymentService->initialize($paymentMethod);
            return $payment->pay($requestData, $request->all());
        } else {
            //inseting request data to the session
            session(['tenant_id' => $requestData->id, 'subscription_type' => $requestData->subscription_type, 'package_id' => $requestData->package_id, 'company_name' => $requestData->company_name, 'phone_number' => $requestData->phone_number, 'email' => $requestData->email, 'name' => $requestData->name, 'password' => $requestData->password, 'tenant' => $requestData->tenant, 'renewal' => $requestData->renewal, 'expiry_date' => $requestData->expiry_date, 'modules' => $requestData->modules, 'permission_ids' => $requestData->permission_ids, 'abandoned_permission_ids' => $requestData->abandoned_permission_ids, 'price' => $requestData->price, 'payment_type' => $requestData->payment_type]);
            $payment = $paymentService->initialize($paymentMethod);
            if ($paymentMethod == 'paystack')
                $requestData->paystack_secret_key = $request->paystack_secret_key;

            return $payment->pay($requestData, $request->all());
        }
    }


    public function paymentPayCancel($paymentMethod, PaymentService $paymentService)
    {
        $payment = $paymentService->initialize($paymentMethod);
        return $payment->cancel();
    }

    public function success()
    {
        if (session()->get('tenant')) {
            $requestData = new Request();
            $requestData->subscription_type = session()->get('subscription_type');
            $requestData->package_id = session()->get('package_id');
            $requestData->company_name = session()->get('company_name');
            $requestData->phone_number = session()->get('phone_number');
            $requestData->email = session()->get('email');
            $requestData->name = session()->get('name');
            $requestData->password = session()->get('password');
            $requestData->tenant = session()->get('tenant');
            $requestData->expiry_date = session()->get('expiry_date');
            $this->createTenant($requestData);
            session(['tenant' => 0]);
            return \Redirect::to('https://' . $requestData->tenant . '.' . env('CENTRAL_DOMAIN'));
        } elseif (session()->get('renewal')) {
            $tenant = Tenant::find(session()->get('tenant_id'));
            $tenant->update(['expiry_date' => session()->get('expiry_date'), 'package_id' => session()->get('package_id'), 'subscription_type' => session()->get('subscription_type')]);
            TenantPayment::create(['tenant_id' => session()->get('tenant_id'), 'amount' => session()->get('price'), 'paid_by' => session()->get('payment_type')]);

            $this->changePermission(
                $tenant,
                session()->get('abandoned_permission_ids'),
                session()->get('permission_ids'),
                session()->get('package_id'),
                session()->get('modules'),
                session()->get('expiry_date'),
                session()->get('subscription_type')
            );

            return \Redirect::to('https://' . session()->get('tenant_id') . '.' . env('CENTRAL_DOMAIN'));
        }
    }

    public function BkashSuccess(Request $request, PaymentService $paymentService)
    {
        $payment = $paymentService->initialize('bkash');
        if ($payment->paymentStatusCheck($request))
            return $this->success();
        else
            return redirect('/');
    }

    public function SslSuccess(Request $request)
    {
        // return $request;
        if (isset($request->value_a)) {
            $user_data = explode(',',  $request->value_a);
            //return $user_data;
            if ($user_data[9] == 'tenant') {
                $requestData = new Request();
                $requestData->subscription_type = $user_data[0];
                $requestData->package_id = $user_data[1];
                $requestData->company_name = $user_data[2];
                $requestData->phone_number = $user_data[3];
                $requestData->email = $user_data[4];
                $requestData->name = $user_data[5];
                $requestData->password = $user_data[6];
                $requestData->expiry_date = $user_data[7];
                $requestData->tenant = $user_data[8];
                $this->createTenant($requestData);
                session(['tenant' => 0]);
                return \Redirect::to('https://' . $requestData->tenant . '.' . env('CENTRAL_DOMAIN'));
            } else {
                $permission_ids = [];
                if (!empty($request->value_c)) {
                    $permission_ids = explode(',', $request->value_c);
                }
                $tenant = Tenant::find($user_data[8]);
                $tenant->update(['expiry_date' => $user_data[7], 'package_id' => $user_data[1], 'subscription_type' => $user_data[0]]);
                TenantPayment::create(['tenant_id' => $user_data[8], 'amount' => $request->amount, 'paid_by' => 'SSL_Commerz']);

                $this->changePermission(
                    $tenant,
                    session()->get('abandoned_permission_ids'),
                    json_encode($permission_ids),
                    $user_data[1],
                    session()->get('modules'),
                    $user_data[7],
                    $user_data[0]
                );
                return \Redirect::to('https://' . $user_data[8] . '.' . env('CENTRAL_DOMAIN'));
            }
        }
    }

    /*
    |-----------
    |Paystack
    |-----------
    */
    public function handleGatewayCallback(PaymentService $paymentService)
    {
        $payment = $paymentService->initialize('paystack');
        return $payment->paymentCallback();
    }
}
