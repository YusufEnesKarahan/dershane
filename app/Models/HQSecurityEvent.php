<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSecurityEvent extends Model
{
    use HasFactory;

    protected $table = 'hq_security_events';

    protected $fillable = [
        'event_type',
        'severity',
        'tenant_id',
        'user_id',
        'ip',
        'metadata',
        'resolved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
