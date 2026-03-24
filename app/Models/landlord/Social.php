<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $fillable =[
        "name", "link", "icon", "order"
    ];
}
