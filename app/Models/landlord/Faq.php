<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ["question", "answer", "order"];
}
