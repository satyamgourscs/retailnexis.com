<?php
declare(strict_types=1);

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BkashPayment implements PaybleContract
{
    public $errorCodes = [
        2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010,
        2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020,
        2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030,
        2031, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039, 2040,
        2041, 2042, 2043, 2044, 2045, 2046, 2047, 2048, 2049, 2050,
        2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059, 2060,
        2061, 2062, 2063, 2064, 2065, 2066, 2067, 2068, 2069,
        9999, 503,
    ];

    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;
    private $callbackURL;


    public function __construct()
    {
        $pg = DB::table('external_services')->where('name','bkash')->where('type','payment')->first();
        $lines = explode(';',$pg->details);
        $vals = explode(',', $lines[1]);
        if($vals[0] == 'sandbox'){
            $this->base_url = config('bkash.bkash_sandbox_url');
        }elseif($vals[0] == 'live'){
            $this->base_url = config('bkash.bkash_base_url');
        }
        $this->app_key = $vals[1];
        $this->app_secret = $vals[2];
        $this->username = $vals[3];
        $this->password = $vals[4];
        $this->callbackURL = config('bkash.bkash_callback_url');
    }


    public function pay($request, $otherRequest)
    {
        $grantTokenData = $this->grantToken();

        if(!isset($grantTokenData['id_token']) && in_array($grantTokenData['statusCode'], $this->errorCodes))
        {
            $statusCode = $grantTokenData['statusCode'];
            $statusMessage = $grantTokenData['statusMessage'];
            session()->flash('message',"$statusCode | $statusMessage ");
            return redirect(route("payment.pay.page",'bkash'), 307);
        }

        Session::put('id_token', json_encode($grantTokenData['id_token']));

        $createPaymentObjectData = $this->createPayment($request->price, $grantTokenData['id_token']);

        if(isset($createPaymentObjectData->statusCode) && in_array($createPaymentObjectData->statusCode, $this->errorCodes)) {
            $statusCode = $createPaymentObjectData->statusCode;
            $statusMessage = $createPaymentObjectData->statusMessage;
            session()->flash('message',"$statusCode | $statusMessage ");
            return redirect(route("payment.pay.page",'bkash'), 307);
        }

        return redirect()->away($createPaymentObjectData->{'bkashURL'});
    }

    private function grantToken() : array
    {
        $_SESSION['id_token'] = null;
        $post_token = array(
            'app_key'=> $this->app_key,
            'app_secret'=> $this->app_secret
        );

        $url = curl_init("$this->base_url/checkout/token/grant");

        $request_data_json=json_encode($post_token);
        $header = array(
            'Content-Type: application/json',
            "username:$this->username",
            "password:$this->password"
        );

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result_data = curl_exec($url);
        curl_close($url);

        $response = json_decode($result_data, true);

        return $response;
    }

    private function createPayment($totalAmount, string $idToken): object
    {
        $auth = $idToken;
        $requestbody = array(
            'mode' => '0011',
            'amount' => $totalAmount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'payerReference' => '01XXXXXXXXX',
            'merchantInvoiceNumber' => rand(),
            'callbackURL' => $this->callbackURL
        );

        $url = curl_init("$this->base_url/checkout/create");
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type: application/json',
            'Authorization: ' . $auth,
            "X-APP-Key: $this->app_key"
        );


        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        $obj = json_decode($resultdata);

        return $obj;
    }

    public function paymentStatusCheck(Request $request) : bool
    {
        if(isset($request->paymentID)){
            if($request->status==="success") {
                $executePaymentObjectData = $this->excecutePayment($request->paymentID);
                if(isset($executePaymentObjectData->statusCode) && in_array($executePaymentObjectData->statusCode, $this->errorCodes)) {
                    $statusCode = $executePaymentObjectData->statusCode;
                    $statusMessage = $executePaymentObjectData->statusMessage;
                    session()->flash('message',"$statusCode | $statusMessage ");

                    return false;
                }
                return true;
            }
        }
        session()->flash('message','Something Wrong !!!. Payment failed. Please try again later');

        return false;
    }

    private function excecutePayment($paymentID)
    {
        $auth = json_decode(Session::get('id_token'));

        $post_token = array(
            'paymentID' => $paymentID
        );

        $url = curl_init("$this->base_url/checkout/execute");
        $posttoken = json_encode($post_token);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            "X-APP-Key: $this->app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);

        curl_close($url);

        $obj = json_decode($resultdata);

        return $obj;
    }


    public function cancel()
    {

    }


}

