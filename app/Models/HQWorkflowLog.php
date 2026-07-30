<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQWorkflowLog extends Model
{
    protected $table = 'hq_workflow_logs';

    protected $fillable = [
        'hq_workflow_run_id',
        'hq_workflow_execution_id',
        'level', // info, warning, error
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function run()
    {
        return $this->belongsTo(HQWorkflowRun::class, 'hq_workflow_run_id');
    }

    public function execution()
    {
        return $this->belongsTo(HQWorkflowExecution::class, 'hq_workflow_execution_id');
    }
}
