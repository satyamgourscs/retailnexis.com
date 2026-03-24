<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Central (landlord) SaaS routes must use saleprosaas_landlord even when .env leaves DB_CONNECTION=mysql.
 */
final class LandlordConnection
{
    public static function ensureSaleprosaasLandlordIsDefault(): void
    {
        if (empty(config('app.landlord_db'))) {
            return;
        }
        if (empty(config('database.connections.saleprosaas_landlord'))) {
            return;
        }
        DB::setDefaultConnection('saleprosaas_landlord');
    }
}
