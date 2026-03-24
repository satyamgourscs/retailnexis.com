<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'subject', 'description', 'superadmin', 'tenant_id', 'parent_ticket_id'
    ];

}
