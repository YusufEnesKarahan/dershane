<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    protected $fillable = ['lead_id', 'user_id', 'followup_date', 'reminder_note', 'priority', 'status'];

    protected $casts = [
        'followup_date' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
