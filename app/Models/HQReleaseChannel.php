<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQReleaseChannel extends Model
{
    use HasFactory;

    protected $table = 'hq_release_channels';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function tenants()
    {
        return $this->hasMany(HQTenant::class, 'hq_release_channel_id');
    }
}
