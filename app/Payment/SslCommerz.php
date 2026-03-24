<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
// use App\Library\SslCommerz\SslCommerzNotification;
// use App\Traits\PaymentTrait;
use Exception;
use DB;

class SslCommerz implements PaybleContract
{
    public function pay($request, $otherRequest)
    {
        $pg = DB::table('external_services')->where('name','sslcommerz')->where('type','payment')->first();
        $lines = explode(';',$pg->details);
        $vals = explode(',', $lines[1]);
        $post_data = array();
        $post_data['store_id'] = $vals[0];
        $post_data['store_passwd'] =  $vals[1];
        $post_data['total_amount'] = $request->price;
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
        $base = rtrim((string) config('app.url'), '/');
        $post_data['success_url'] = $base.'/ssl_payment_success';
        $post_data['fail_url'] = $base;
        $post_data['cancel_url'] = $base;
        // return $post_data;
        # EMI INFO
        $post_data['emi_option'] = "1";
        $post_data['emi_max_inst_option'] = "9";
        $post_data['emi_selected_inst'] = "9";

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $request->company_name;
        $post_data['cus_email'] = $request->email;
        $post_data['cus_add1'] = 'unknown';
        $post_data['cus_add2'] = 'unknown';
        $post_data['cus_city'] = 'unknown';
        $post_data['cus_state'] = 'unknown';
        $post_data['cus_postcode'] = "1000";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $request->phone_number;
        $post_data['cus_fax'] = '';

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = $request->company_name;
        $post_data['ship_add1'] = "unknown";
        $post_data['ship_add2'] = "unknown";
        $post_data['ship_city'] = "unknown";
        $post_data['ship_state'] = "unknown";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_country'] = "Bangladesh";
        $post_data['shipping_method'] = 'free';
        $post_data['product_name'] = "Unknown";
        $post_data['product_category'] = "Unknown";
        $post_data['product_profile'] = "Unknown";

        # OPTIONAL PARAMETERS
        $user_data = [
            'subscription_type' => $request->subscription_type,
            'package_id' => $request->package_id,
            'company_name' => $request->company_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'name' => $request->name,
            'password' => $request->password,
            'expiry_date' => $request->expiry_date,
        ];
        if(isset($request->tenant)) {
            $user_data['id'] = $request->tenant;
            $user_data['type '] = "tenant";
        }
        elseif(isset($request->renewal)){
            $user_data['id'] = $request->id;
            $user_data['type '] = "renewal";
            //$user_data['allDomains'] = ;
            //$user_data['permission_ids'] = ;
            $user_data['modules'] = $request->modules;
        }

        $post_data['value_a'] = implode(',', $user_data);
        if(isset($request->permission_ids))
            $post_data['value_c'] = implode(',', json_decode($request->permission_ids));
        else
            $post_data['value_c'] ='';
        $post_data['value_d'] = "ref004";

        # CART PARAMETERS
        $post_data['cart'] = json_encode(array(
            array("product"=>"Subscription fee","amount"=>$request->price),
        ));
        $post_data['product_amount'] = $request->price;
        $post_data['vat'] = "0";
        $post_data['discount_amount'] = "0";
        $post_data['convenience_fee'] = "0";
        # REQUEST SEND TO SSLCOMMERZ
        //return $post_data;
        //$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";
        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if($code == 200 && !(curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

        // return $sslcommerzResponse;
        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true);
        //return $sslcz;
        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
                # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
                # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            echo "JSON Data parsing error!";
        }
    }

    public function cancel()
    {

    }
}

?>
