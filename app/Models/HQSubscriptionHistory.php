<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSubscriptionHistory extends Model
{
    use HasFactory;

    protected $table = 'hq_subscription_history';

    protected $fillable = [
        'subscription_id',
        'old_plan_id',
        'new_plan_id',
        'action',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function subscription()
    {
        return $this->belongsTo(HQSubscription::class, 'subscription_id');
    }

    public function oldPlan()
    {
        return $this->belongsTo(HQSubscriptionPlan::class, 'old_plan_id');
    }

    public function newPlan()
    {
        return $this->belongsTo(HQSubscriptionPlan::class, 'new_plan_id');
    }
}
