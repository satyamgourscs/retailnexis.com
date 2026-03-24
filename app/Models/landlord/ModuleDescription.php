<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleDescription extends Model
{
    use HasFactory;

    protected $fillable = ['heading', 'sub_heading', 'image', 'lang_id'];
}
