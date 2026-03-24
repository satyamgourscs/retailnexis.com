<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
use App\Models\landlord\GeneralSetting;
use DB;
class PesapalPayment implements PaybleContract
{
    public function cancel()
    {
        return redirect('/');
    }

    public function registerIPN()
    {
        $pg = DB::table('external_services')->where('name','Pesapal')->where('type','payment')->first();
        $lines = explode(';',$pg->details);
        $keys = explode(',', $lines[0]);
        $vals = explode(',', $lines[1]);

        $results = array_combine($keys, $vals);

        $APP_ENVIROMENT = $results['Mode'];

        $token = $this->accessToken();

        if($APP_ENVIROMENT == 'sandbox'){
            $ipnRegistrationUrl = "https://cybqa.pesapal.com/pesapalv3/api/URLSetup/RegisterIPN";
        }elseif($APP_ENVIROMENT == 'live'){
            $ipnRegistrationUrl = "https://pay.pesapal.com/v3/api/URLSetup/RegisterIPN";
        }else{
            echo "Invalid APP_ENVIROMENT";
            exit;
        }
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer $token"
        );
        $data = array(
            "url" => "https://12eb-41-81-142-80.ngrok-free.app/pesapal/pin.php",
            "ipn_notification_type" => "POST"
        );
        $ch = curl_init($ipnRegistrationUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response);
        return $data;
        // $ipn_id = $data->ipn_id;
        // $ipn_url = $data->url;
    }

    public function pesapalIPN()
    {
        return "PESAPAL IPN";
    }

    public function accessToken()
    {
        $pg = DB::table('external_services')->where('name','Pesapal')->where('type','payment')->first();
        $lines = explode(';',$pg->details);
        $keys = explode(',', $lines[0]);
        $vals = explode(',', $lines[1]);

        $results = array_combine($keys, $vals);

        $APP_ENVIROMENT = $results['Mode'];
        // return $APP_ENVIROMENT;
        if($APP_ENVIROMENT == 'sandbox'){
            $apiUrl = "https://cybqa.pesapal.com/pesapalv3/api/Auth/RequestToken"; // Sandbox URL
            $consumerKey = $results['Consumer Key'];
            $consumerSecret = $results['Consumer Secret'];
        }elseif($APP_ENVIROMENT == 'live'){
            $apiUrl = "https://pay.pesapal.com/v3/api/Auth/RequestToken"; // Live URL
            $consumerKey = $results['Consumer Key'];;
            $consumerSecret = $results['Consumer Secret'];
        }else{
            echo "Invalid APP_ENVIROMENT";
            exit;
        }
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];
        $data = [
            "consumer_key" => $consumerKey,
            "consumer_secret" => $consumerSecret
        ];
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response);

        $token = $data->token;

        return $token;
    }

    public function pay($request, $otherRequest)
    {
        $data = $request;

        $pg = DB::table('external_services')->where('name','Pesapal')->where('type','payment')->first();

        $lines = explode(';',$pg->details);
        $keys = explode(',', $lines[0]);
        $vals = explode(',', $lines[1]);

        $results = array_combine($keys, $vals);

        $lims_general_setting_data = GeneralSetting::latest()->first();
        $company = $lims_general_setting_data->site_title;

        $APP_ENVIROMENT = $results['Mode'];
        $token = $this->accessToken();
        $ipnData = $this->registerIPN();

        $merchantreference = rand(1, 1000000000000000000);
        $phone = $data->phone_number; //0768168060
        $amount = $data->price;
        $callbackurl = "salepro.test/ipn";
        $branch = $company;
        $first_name = $data->name;
        //$middle_name = "Coders";
        $last_name = $data->name;
        $email_address = $data->email ? $data->email : "hello@lion-coders.com";
        if( $APP_ENVIROMENT == 'sandbox'){
        $submitOrderUrl = "https://cybqa.pesapal.com/pesapalv3/api/Transactions/SubmitOrderRequest";
        }elseif($APP_ENVIROMENT == 'live'){
        $submitOrderUrl = "https://pay.pesapal.com/v3/api/Transactions/SubmitOrderRequest";
        }else{
        echo "Invalid APP_ENVIROMENT";
        exit;
        }
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer $token"
        );

        // Request payload
        $data = array(
            "id" => "$merchantreference",
            "currency" => "KES",
            "amount" => $amount,
            "description" => "Payment description goes here",
            "callback_url" => "$ipnData->url",
            "notification_id" => "$ipnData->ipn_id",
            "branch" => "$branch",
            "billing_address" => array(
                "email_address" => "$email_address",
                "phone_number" => "$phone",
                "country_code" => "KE",
                "first_name" => "$first_name",
                //"middle_name" => "$middle_name",
                "last_name" => "$last_name",
                "line_1" => "Pesapal Limited",
                "line_2" => "",
                "city" => "",
                "state" => "",
                "postal_code" => "",
                "zip_code" => ""
            )
        );
        $ch = curl_init($submitOrderUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        // $redirectUrl = $data->redirect_url;
        // // return $redirectUrl;
        // echo "<script>window.location.href='$redirectUrl'</script>";
        if ($responseCode === 200) {
        // Check for errors in the response
            if (isset($data['error'])) {
                $errorMessage = $data['error']['message'];
                $errorCode = $data['error']['code'];
                // return response()->json([
                //     'status' => 'error',
                //     'message' => $data['error']['message'],
                //     'code' => $data['error']['code']
                // ], $responseCode);
                return "<div style='color: red; text-align: center; margin-top: 20px;'>
                            <h3>Error Occurred</h3>
                            <p>Message: $errorMessage</p>
                            <p>Code: $errorCode</p>
                            <p>Please contact support for assistance.</p>
                            <button onclick='window.history.back()'
                                    style='padding: 10px 20px; font-size: 16px; color: white; background-color: #007BFF; border: none; border-radius: 5px; cursor: pointer;'>
                                Go Back
                            </button>
                        </div>";
            }

            // Redirect to the payment URL if no errors
            $redirectUrl = $data['redirect_url'];
            echo "<script>window.location.href='$redirectUrl'</script>";
        } else {
            // Handle HTTP errors
            return response()->json([
                'status' => 'error',
                'message' => $data['error']['message'] ?? 'An unexpected error occurred.',
                'code' => $data['error']['code'] ?? 'unknown_error'
            ], $responseCode);
        }
    }
}
