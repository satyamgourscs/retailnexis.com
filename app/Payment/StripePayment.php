<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
use App\Models\landlord\GeneralSetting;
use Stripe\Stripe;
use DB;

class StripePayment implements PaybleContract
{
    public function pay($request, $otherRequest)
    {
        $totalAmount = $request->price * 100;
        $paymentMethodId = $otherRequest['paymentMethodId'];


        try {
            $general_setting = GeneralSetting::latest()->first();

            $pg = DB::table('external_services')->where('name','stripe')->where('type','payment')->first();
            $lines = explode(';',$pg->details);
            $vals = explode(',', $lines[1]);
            // Set your secret API key
            \Stripe\Stripe::setApiKey($vals[1]);

            // Step 1: Create or retrieve the customer
            $customer = \Stripe\Customer::create([
                'name' => 'Anonymous Customer',
            ]);

            // Step 2: Attach the Payment Method to the Customer
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customer->id]);

            // Step 3: Set the Payment Method as the default for future payments
            \Stripe\Customer::update($customer->id, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
            ]);

            // Step 4: Create a PaymentIntent to charge the customer
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $totalAmount * 100, // Amount in cents
                'currency' => $general_setting->currency,
                'customer' => $customer->id,
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true, // Automatically confirm the payment
            ]);

            return ['success' => true, 'paymentIntent' => $paymentIntent];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Catch Stripe-specific exceptions
            return ['error' => $e->getMessage()];
        }

        /*
        |----------------------------------------------------------
        | Store your data here.
        | This section is common for all. Suggest you to use Trait.
        |----------------------------------------------------------
        */

        return response()->json(['success' =>'done']);
    }

    public function cancel(){
        // $this->orderCancel();
        // return response()->json('success');

        /*
        |----------------------------------------------------------
        | Store your data here.
        | This section is common for all. Suggest you to use Trait.
        |----------------------------------------------------------
        */
    }

}

?>
