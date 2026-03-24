<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiryDateToTenantsTable extends Migration
{
    protected function connectionName(): string
    {
        return (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $c = $this->connectionName();

        if (! Schema::connection($c)->hasTable('tenants')) {
            return;
        }

        if (Schema::connection($c)->hasColumn('tenants', 'expiry_date')) {
            return;
        }

        $afterDbId = Schema::connection($c)->hasColumn('tenants', 'db_id');

        try {
            Schema::connection($c)->table('tenants', function (Blueprint $table) use ($afterDbId) {
                if ($afterDbId) {
                    $table->date('expiry_date')->after('db_id')->nullable();
                } else {
                    $table->date('expiry_date')->nullable();
                }
            });
        } catch (\Throwable $e) {
            // Hostinger / stale schema cache: column may already exist; hasColumn can miss cross-connection edge cases.
            if (str_contains($e->getMessage(), '1060') || str_contains($e->getMessage(), 'Duplicate column')) {
                return;
            }
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $c = $this->connectionName();

        if (! Schema::connection($c)->hasTable('tenants') || ! Schema::connection($c)->hasColumn('tenants', 'expiry_date')) {
            return;
        }

        Schema::connection($c)->table('tenants', function (Blueprint $table) {
            $table->dropColumn('expiry_date');
        });
    }
}
