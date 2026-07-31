<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQComplianceResult extends Model
{
    use HasFactory;

    protected $table = 'hq_compliance_results';

    protected $fillable = [
        'tenant_id',
        'framework_id',
        'score_percentage',
        'details',
        'evaluated_at',
    ];

    protected $casts = [
        'score_percentage' => 'decimal:2',
        'details' => 'array',
        'evaluated_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }

    public function framework()
    {
        return $this->belongsTo(HQComplianceFramework::class, 'framework_id');
    }
}
