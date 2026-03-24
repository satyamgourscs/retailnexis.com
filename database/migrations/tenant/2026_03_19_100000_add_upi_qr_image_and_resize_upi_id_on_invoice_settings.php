<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('invoice_settings', 'upi_qr_image')) {
                if (Schema::hasColumn('invoice_settings', 'upi_id')) {
                    $table->string('upi_qr_image', 191)->nullable()->after('upi_id');
                } else {
                    $table->string('upi_qr_image', 191)->nullable();
                }
            }
        });

        if (Schema::hasColumn('invoice_settings', 'upi_id')) {
            Schema::table('invoice_settings', function (Blueprint $table) {
                $table->string('upi_id', 50)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoice_settings', 'upi_id')) {
            Schema::table('invoice_settings', function (Blueprint $table) {
                $table->string('upi_id', 255)->nullable()->change();
            });
        }

        Schema::table('invoice_settings', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_settings', 'upi_qr_image')) {
                $table->dropColumn('upi_qr_image');
            }
        });
    }
};
