<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQWorkflowExecution extends Model
{
    protected $table = 'hq_workflow_executions';

    protected $fillable = [
        'hq_workflow_run_id',
        'hq_workflow_step_id',
        'status', // pending, running, success, failed, skipped
        'input_data',
        'output_data',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function run()
    {
        return $this->belongsTo(HQWorkflowRun::class, 'hq_workflow_run_id');
    }

    public function step()
    {
        return $this->belongsTo(HQWorkflowStep::class, 'hq_workflow_step_id');
    }
}
