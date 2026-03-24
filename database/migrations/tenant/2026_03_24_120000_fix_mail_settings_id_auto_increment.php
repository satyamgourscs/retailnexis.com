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

        $last = null;
        foreach (
            [
                'ALTER TABLE `mail_settings` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT',
                'ALTER TABLE `mail_settings` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT',
            ] as $sql
        ) {
            try {
                DB::statement($sql);
                $last = null;
                break;
            } catch (\Throwable $e) {
                $last = $e;
            }
        }
        if ($last !== null) {
            throw $last;
        }
    }

    public function down(): void
    {
        //
    }
};
