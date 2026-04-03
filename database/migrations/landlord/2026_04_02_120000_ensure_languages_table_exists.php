<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = (string) config('tenancy.database.central_connection', 'retailnexis_landlord');

        if (Schema::connection($connection)->hasTable('languages')) {
            return;
        }

        Schema::connection($connection)->create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        DB::connection($connection)->table('languages')->insert([
            'code' => 'en',
            'name' => 'English',
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
    }
};
