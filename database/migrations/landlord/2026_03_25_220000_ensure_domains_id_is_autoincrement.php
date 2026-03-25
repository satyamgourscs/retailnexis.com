<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: SQLSTATE[HY000]: 1364 Field 'id' doesn't have a default value on insert into `domains`.
     * Production DBs sometimes lack AUTO_INCREMENT on `domains.id` (import/manual DDL).
     */
    public function up(): void
    {
        $connection = (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');

        if (! Schema::connection($connection)->hasTable('domains')) {
            return;
        }

        $driver = config("database.connections.{$connection}.driver");
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $database = config("database.connections.{$connection}.database");
        if (! is_string($database) || $database === '') {
            return;
        }

        $row = DB::connection($connection)->selectOne(
            'SELECT COLUMN_TYPE, EXTRA, COLUMN_KEY
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$database, 'domains', 'id']
        );

        if (! $row || ! isset($row->COLUMN_TYPE)) {
            return;
        }

        if (stripos((string) $row->EXTRA, 'auto_increment') !== false) {
            return;
        }

        if (! preg_match('/int/i', (string) $row->COLUMN_TYPE)) {
            return;
        }

        $columnType = $row->COLUMN_TYPE;
        DB::connection($connection)->statement(
            "ALTER TABLE `domains` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT"
        );
    }

    public function down(): void
    {
        // Dropping AUTO_INCREMENT is unsafe; leave schema as-is.
    }
};
