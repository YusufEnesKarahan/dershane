<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQWorkflowStep extends Model
{
    protected $table = 'hq_workflow_steps';

    protected $fillable = [
        'hq_workflow_id',
        'type', // 'condition', 'action', 'delay'
        'name',
        'config',
        'next_step_id',
        'fallback_step_id',
        'order_index',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(HQWorkflow::class, 'hq_workflow_id');
    }

    public function nextStep()
    {
        return $this->belongsTo(HQWorkflowStep::class, 'next_step_id');
    }

    public function fallbackStep()
    {
        return $this->belongsTo(HQWorkflowStep::class, 'fallback_step_id');
    }
}
