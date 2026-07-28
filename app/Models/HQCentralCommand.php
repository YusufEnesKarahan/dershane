<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQCentralCommand extends Model
{
    use HasFactory;

    protected $table = 'hq_central_commands';

    protected $fillable = [
        'system_instance_id',
        'command_type',
        'payload',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function instance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }
}
