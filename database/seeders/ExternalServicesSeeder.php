<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExternalServicesSeeder extends Seeder
{
    public function run()
    {
        $general_settings = DB::table('general_settings')->latest()->first();

        if (!DB::table('external_services')->where('name', 'paypal')->count()) {
            DB::table('external_services')->insert([
                'name' => 'paypal',
                'type' => 'payment',
                'details' => 'Client ID,Client Secret;' . $general_settings->paypal_client_id . ',' . $general_settings->paypal_client_secret,
                'active' => Str::contains($general_settings->active_payment_gateway, 'paypal') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'stripe')->count()) {
            DB::table('external_services')->insert([
                'name' => 'stripe',
                'type' => 'payment',
                'details' => 'Public Key,Private Key;' . $general_settings->stripe_public_key . ',' . $general_settings->stripe_secret_key,
                'active' => Str::contains($general_settings->active_payment_gateway, 'stripe') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'razorpay')->count()) {
            DB::table('external_services')->insert([
                'name' => 'razorpay',
                'type' => 'payment',
                'details' => 'Key,Secret;' . $general_settings->razorpay_key . ',' . $general_settings->razorpay_secret,
                'active' => Str::contains($general_settings->active_payment_gateway, 'razorpay') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'paystack')->count()) {
            DB::table('external_services')->insert([
                'name' => 'paystack',
                'type' => 'payment',
                'details' => 'public_Key,Secret_Key;' . $general_settings->paystack_public_key . ',' . $general_settings->paystack_secret_key,
                'active' => Str::contains($general_settings->active_payment_gateway, 'paystack') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'paydunya')->count()) {
            DB::table('external_services')->insert([
                'name' => 'paydunya',
                'type' => 'payment',
                'details' => 'master_Key,public_Key,Secret_Key,token;' . $general_settings->paydunya_master_key . ',' . $general_settings->paydunya_public_key . ',' . $general_settings->paydunya_secret_key . ',' . $general_settings->paydunya_token,
                'active' => Str::contains($general_settings->active_payment_gateway, 'paydunya') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'bkash')->count()) {
            DB::table('external_services')->insert([
                'name' => 'bkash',
                'type' => 'payment',
                'details' => 'Mode,app_key,app_secret,username,password;sandbox,' . $general_settings->bkash_app_key . ',' . $general_settings->bkash_app_secret . ',' . $general_settings->bkash_username . ',' . $general_settings->bkash_password,
                'active' => Str::contains($general_settings->active_payment_gateway, 'bkash') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'sslcommerz')->count()) {
            DB::table('external_services')->insert([
                'name' => 'sslcommerz',
                'type' => 'payment',
                'details' => 'store_id,store_password;' . $general_settings->ssl_store_id . ',' . $general_settings->ssl_store_password,
                'active' => Str::contains($general_settings->active_payment_gateway, 'ssl_commerz') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!DB::table('external_services')->where('name', 'pesapal')->count()) {
            DB::table('external_services')->insert([
                'name' => 'pesapal',
                'type' => 'payment',
                'details' => 'Mode,Consumer Key,Consumer Secret;sandbox,qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW,osGQ364R49cXKeOYSpaOnT++rHs=',
                'active' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
