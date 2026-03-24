<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');

        Schema::connection($connection)->create('tenant_databases', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->unique()->comment('MySQL database name (pre-created in hPanel)');
            $table->boolean('is_used')->default(false)->index();
            $table->string('tenant_id', 191)->nullable()->unique();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $connection = (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');
        Schema::connection($connection)->dropIfExists('tenant_databases');
    }
};
