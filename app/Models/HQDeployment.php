<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQDeployment extends Model
{
    use HasFactory;

    protected $table = 'hq_deployments';

    protected $fillable = [
        'version',
        'type',
        'status',
        'rollout_percentage',
        'created_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'rollout_percentage' => 'integer',
    ];

    public function targets()
    {
        return $this->hasMany(HQDeploymentTarget::class, 'hq_deployment_id');
    }

    public function logs()
    {
        return $this->hasMany(HQDeploymentLog::class, 'hq_deployment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
