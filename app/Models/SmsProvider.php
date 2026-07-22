<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsProvider extends Model
{
    protected $fillable = ['name', 'code', 'api_key', 'is_active'];
}
