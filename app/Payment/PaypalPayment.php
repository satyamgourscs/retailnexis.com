<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
// use App\Traits\PaymentTrait;

class PaypalPayment implements PaybleContract
{
    // use PaymentTrait;

    public function pay($request, $otherRequest)
    {
        $request->payment_status = "completed";
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
        return response()->json('success');
    }
}

?>
