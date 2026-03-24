<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        "text", "title", "slug", "meta_title", "meta_description"
    ];
}
