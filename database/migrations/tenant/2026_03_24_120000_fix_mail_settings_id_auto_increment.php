<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Same fix as landlord: tenant mail_settings from old dumps may lack AUTO_INCREMENT on id.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mail_settings')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            'ALTER TABLE `mail_settings` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
        );
    }

    public function down(): void
    {
        //
    }
};
