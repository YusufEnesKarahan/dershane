<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionLog extends Model
{
    protected $fillable = [
        'subscription_id',
        'action',
        'old_plan_id',
        'new_plan_id',
        'notes',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function oldPlan()
    {
        return $this->belongsTo(Plan::class, 'old_plan_id');
    }

    public function newPlan()
    {
        return $this->belongsTo(Plan::class, 'new_plan_id');
    }
}
