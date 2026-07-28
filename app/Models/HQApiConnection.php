<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQApiConnection extends Model
{
    use HasFactory;

    protected $table = 'hq_api_connections';

    protected $fillable = [
        'system_instance_id',
        'token_hash',
        'last_request_at',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'last_request_at' => 'datetime',
    ];

    public function instance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }
}
