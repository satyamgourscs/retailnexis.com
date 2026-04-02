<?php

namespace App\Models\landlord;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'package_id',
        'subscription_type',
        'expiry_date',
        'company_name',
        'phone_number',
        'email',
    ];

    /**
     * Real DB columns on `tenants` (not stored only inside JSON `data`).
     *
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
            'data',
            'db_id',
            'expiry_date',
        ];
    }
}
