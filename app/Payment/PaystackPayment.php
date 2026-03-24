<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
// use App\Traits\PaymentTrait;

class PaystackPayment implements PaybleContract
{
    // use PaymentTrait;

    public function pay($request, $otherRequest)
    {
        $request->reference    = date('Ymdhis');
        $request->amount       = $request->price * 100;
        //For test
        //$request->currency     = 'NGN';
        //For test
        $request->currency     = \DB::table('general_settings')->latest()->first()->currency;
        $request->callback_url = route('payment.success');

        $pay = json_decode($this->initiatePayment($request));
        if ($pay) {
            if ($pay->status) {
                return redirect($pay->data->authorization_url);
            } else {
                return back()->withError($pay->message);
            }
        } else {
            return back()->withError("Something went wrong");
        }
    }

    public function paymentCallback()
    {
        $response = json_decode($this->verifyPayment(request('reference')));
        if ($response) {
            $reference  = $response->data->reference;
            if ($response->status == 'success') {
                $payment_id = $response->data->id;
                // return $this->updateOrderAfterPaymentComplete($reference, $payment_id);
            } else {
                // $this->undoTableDataAndRestoreProductQuantity($reference);
                return redirect('order_cancel')->withError($response->message);
            }
        } else {
            // $this->latestOrderCancel();
            return redirect('order_cancel')->withError("Something went wrong");
        }
    }

    protected function initiatePayment($formData)
    {
        $url = "https://api.paystack.co/transaction/initialize";

        $fields_string = http_build_query($formData);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $formData->paystack_secret_key,
            "Cache-Control: no-cache",
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //For test from local server, Disable SSL verification, not needed in online
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //For test from local server, Disable SSL verification, not needed in online

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    protected function verifyPayment($reference)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return  $response;
    }

    public function cancel()
    {
        // $this->orderCancel();
        return response()->json('success');
    }
}
