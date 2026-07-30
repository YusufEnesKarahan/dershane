<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQBackupRetentionRule extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_retention_rules';

    protected $fillable = [
        'hq_backup_policy_id',
        'rule_type',
    ];

    public function policy()
    {
        return $this->belongsTo(HQBackupPolicy::class, 'hq_backup_policy_id');
    }
}
