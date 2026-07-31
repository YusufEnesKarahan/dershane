<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQPolicyRule extends Model
{
    use HasFactory;

    protected $table = 'hq_policy_rules';

    protected $fillable = [
        'policy_id',
        'metric',
        'operator',
        'value',
        'logical_operator',
    ];

    public function policy()
    {
        return $this->belongsTo(HQPolicy::class, 'policy_id');
    }
}
