<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentNotification extends Model
{
    protected $fillable = ['parent_id', 'title', 'content', 'is_read'];
}
