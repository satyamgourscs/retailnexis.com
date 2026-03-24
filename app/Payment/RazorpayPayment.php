<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
// use App\Traits\PaymentTrait;
use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Facades\Session;

class RazorpayPayment implements PaybleContract
{
    public function pay($request, $otherRequest)
    {
        if(count($otherRequest) && !empty($otherRequest['razorpay_payment_id'])) {
            try {
                /*
                |----------------------------------------------------------
                | Store your data here.
                | This section is common for all. Suggest you to use Trait.
                |----------------------------------------------------------
                */
                Session::forget('error');
                //return $otherRequest->redirectUrl;
                return \Redirect::to($otherRequest['redirectUrl']);
            } catch (Exception $e) {
                Session::put('error',$e->getMessage());
                return redirect(route("payment.pay.page",'razorpay'), 307);
            }
        }
            /*
            |----------------------------------------------------------
            | Apply login with database after payment done
            |----------------------------------------------------------
            */
    }

    public function cancel(){
        // $this->orderCancel();
        return response()->json('success');
    }
}

?>
