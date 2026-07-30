<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQDeploymentTarget extends Model
{
    use HasFactory;

    protected $table = 'hq_deployment_targets';

    protected $fillable = [
        'hq_deployment_id',
        'targetable_type',
        'targetable_id',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function deployment()
    {
        return $this->belongsTo(HQDeployment::class, 'hq_deployment_id');
    }

    public function targetable()
    {
        return $this->morphTo();
    }
}
