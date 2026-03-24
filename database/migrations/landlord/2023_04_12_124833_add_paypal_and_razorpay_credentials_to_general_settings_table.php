<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaypalAndRazorpayCredentialsToGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('paypal_client_id')->after('stripe_secret_key')->nullable();
            $table->string('paypal_client_secret')->after('paypal_client_id')->nullable();
            $table->string('razorpay_number')->after('paypal_client_secret')->nullable();
            $table->string('razorpay_key')->after('razorpay_number')->nullable();
            $table->string('razorpay_secret')->after('razorpay_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            //
        });
    }
}
