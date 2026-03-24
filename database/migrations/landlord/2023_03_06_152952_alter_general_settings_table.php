<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('currency', 10)->default('USD')->after('date_format');
            $table->string('currency_position', 10)->nullable()->after('currency');
            $table->string('meta_title', 100)->nullable()->after('site_title');
            $table->string('meta_description', 200)->nullable()->after('meta_title');
            $table->string('og_title', 100)->nullable()->after('meta_description');
            $table->string('og_description', 200)->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->longText('chat_script')->nullable()->after('free_trial_limit');
            $table->longText('ga_script')->nullable()->after('chat_script');
            $table->longText('fb_pixel_script')->nullable()->after('ga_script');
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
            $table->dropColumn('currency');
            $table->dropColumn('currency_position');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('og_title');
            $table->dropColumn('og_description');
            $table->dropColumn('og_image');
        });
    }
}
