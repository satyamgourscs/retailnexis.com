<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqDescription extends Model
{
    use HasFactory;

    protected $fillable = ['heading', 'sub_heading', 'lang_id'];
}
