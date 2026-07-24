<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = ['lead_id', 'from_user_id', 'to_user_id', 'assigned_at'];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];
}
