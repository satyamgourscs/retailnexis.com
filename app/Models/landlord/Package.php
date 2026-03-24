<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ["name", "is_free_trial", "monthly_fee", "yearly_fee", "number_of_warehouse", "number_of_product", "number_of_invoice", "number_of_user_account", "number_of_employee", "features", "permission_id", "role_permission_values", "is_active"];
}
