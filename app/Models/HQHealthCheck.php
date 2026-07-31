<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQHealthCheck extends Model
{
    use HasFactory;

    protected $table = 'hq_health_checks';

    protected $fillable = [
        'tenant_id',
        'component',
        'status',
        'response_time',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
