<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQDeploymentLog extends Model
{
    use HasFactory;

    protected $table = 'hq_deployment_logs';

    protected $fillable = [
        'hq_deployment_id',
        'hq_system_instance_id',
        'level',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function deployment()
    {
        return $this->belongsTo(HQDeployment::class, 'hq_deployment_id');
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'hq_system_instance_id');
    }
}
