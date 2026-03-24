<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;

    protected $fillable = ["driver", "host", "port", "from_address", "from_name", "username", "password", "encryption"];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if ($model->getKey() !== null) {
                return;
            }
            $max = static::query()->max('id');
            $model->setAttribute('id', $max !== null ? (int) $max + 1 : 1);
        });
    }
}
