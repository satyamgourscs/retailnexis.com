<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MySQL 1364 "Field 'id' doesn't have a default value" when `id` exists but is not AUTO_INCREMENT
 * (e.g. legacy SQL import). Eloquent inserts omit `id` expecting auto-increment.
 */
return new class extends Migration
{
    public function up(): void
    {
        $connection = 'saleprosaas_landlord';

        if (! Schema::connection($connection)->hasTable('mail_settings')) {
            return;
        }

        if (Schema::connection($connection)->getConnection()->getDriverName() !== 'mysql') {
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
                DB::connection($connection)->statement($sql);
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
        // Do not strip AUTO_INCREMENT on rollback (unsafe / data-dependent).
    }
};
