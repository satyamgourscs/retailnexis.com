<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_free_trial');
            $table->double('monthly_fee');
            $table->double('yearly_fee');
            $table->integer('number_of_warehouse');
            $table->integer('number_of_product');
            $table->integer('number_of_invoice');
            $table->integer('number_of_user_account');
            $table->integer('number_of_employee');
            $table->longText('features');
            $table->longText('role_permission_values')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
