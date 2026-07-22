<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentAccessLog extends Model
{
    protected $fillable = ['parent_id', 'ip_address', 'user_agent', 'action'];
}
