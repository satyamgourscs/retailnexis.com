<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair tenant-style dumps that use `language` instead of landlord `code`.
     */
    public function up(): void
    {
        if (! Schema::hasTable('languages')) {
            return;
        }
        if (Schema::hasColumn('languages', 'code') || ! Schema::hasColumn('languages', 'language')) {
            return;
        }

        DB::statement('ALTER TABLE `languages` CHANGE `language` `code` VARCHAR(191) NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('languages')) {
            return;
        }
        if (! Schema::hasColumn('languages', 'code') || Schema::hasColumn('languages', 'language')) {
            return;
        }

        DB::statement('ALTER TABLE `languages` CHANGE `code` `language` VARCHAR(191) NOT NULL');
    }
};
