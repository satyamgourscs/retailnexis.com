<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('provisioning_status')->default('pending')->after('id');
            $table->text('provisioning_error')->nullable()->after('provisioning_status');
            $table->timestamp('provisioning_started_at')->nullable()->after('provisioning_error');
            $table->timestamp('provisioning_completed_at')->nullable()->after('provisioning_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'provisioning_status',
                'provisioning_error',
                'provisioning_started_at',
                'provisioning_completed_at',
            ]);
        });
    }
};

