<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQEntitlement extends Model
{
    use HasFactory;

    protected $table = 'hq_entitlements';

    protected $fillable = [
        'tenant_id',
        'feature_key',
        'limit_value',
        'source',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
