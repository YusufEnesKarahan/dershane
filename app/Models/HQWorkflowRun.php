<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQWorkflowRun extends Model
{
    protected $table = 'hq_workflow_runs';

    protected $fillable = [
        'hq_workflow_id',
        'tenant_id',
        'current_step_id',
        'status', // pending, running, completed, failed, timeout
        'payload',
        'started_at',
        'completed_at',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'payload' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function workflow()
    {
        return $this->belongsTo(HQWorkflow::class, 'hq_workflow_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function currentStep()
    {
        return $this->belongsTo(HQWorkflowStep::class, 'current_step_id');
    }

    public function executions()
    {
        return $this->hasMany(HQWorkflowExecution::class, 'hq_workflow_run_id');
    }

    public function logs()
    {
        return $this->hasMany(HQWorkflowLog::class, 'hq_workflow_run_id');
    }
}
