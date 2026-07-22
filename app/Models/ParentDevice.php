<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentDevice extends Model
{
    protected $fillable = ['parent_id', 'device_token', 'platform', 'is_active'];
}
