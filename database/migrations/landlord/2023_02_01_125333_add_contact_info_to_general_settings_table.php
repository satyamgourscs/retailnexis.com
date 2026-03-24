<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactInfoToGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'phone')) {
                $table->string('phone')->after('developed_by');
            }
            if (! Schema::hasColumn('general_settings', 'email')) {
                $table->string('email')->after('phone');
            }
            if (! Schema::hasColumn('general_settings', 'free_trial_limit')) {
                $table->double('free_trial_limit')->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'free_trial_limit')) {
                $table->dropColumn('free_trial_limit');
            }
            if (Schema::hasColumn('general_settings', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('general_settings', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
}
