<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable =[
        "featured_image","title","slug","description","meta_title","meta_description","og_title","og_description","og_image"
    ];
}
