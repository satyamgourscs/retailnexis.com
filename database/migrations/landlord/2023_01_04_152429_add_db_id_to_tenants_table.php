<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDbIdToTenantsTable extends Migration
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

        if (! Schema::connection($c)->hasTable('tenants') || Schema::connection($c)->hasColumn('tenants', 'db_id')) {
            return;
        }

        Schema::connection($c)->table('tenants', function (Blueprint $table) {
            $table->integer('db_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $c = $this->connectionName();

        if (! Schema::connection($c)->hasTable('tenants') || ! Schema::connection($c)->hasColumn('tenants', 'db_id')) {
            return;
        }

        Schema::connection($c)->table('tenants', function (Blueprint $table) {
            $table->dropColumn('db_id');
        });
    }
}
