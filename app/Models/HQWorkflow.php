<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HQWorkflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_workflows';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'trigger_event',
        'trigger_conditions',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function steps()
    {
        return $this->hasMany(HQWorkflowStep::class, 'hq_workflow_id')->orderBy('order_index');
    }

    public function runs()
    {
        return $this->hasMany(HQWorkflowRun::class, 'hq_workflow_id');
    }

    public function getInitialStep()
    {
        return $this->steps()->first();
    }
}
