<?php

declare(strict_types=1);

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

/**
 * Pre-provisioned tenant MySQL databases (Hostinger pool — no CREATE DATABASE at runtime).
 */
class TenantDatabase extends Model
{
    protected $table = 'tenant_databases';

    public function getConnectionName(): ?string
    {
        return (string) config('tenancy.database.central_connection', 'saleprosaas_landlord');
    }

    protected $fillable = [
        'name',
        'is_used',
        'tenant_id',
        'assigned_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'assigned_at' => 'datetime',
    ];
}
