<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQTenant extends Model
{
    use HasFactory;

    protected $table = 'hq_tenants';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'status',
        'hq_release_channel_id',
        'hq_instance_group_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function instances()
    {
        return $this->hasMany(HQSystemInstance::class, 'tenant_id');
    }

    public function licenses()
    {
        return $this->hasMany(HQLicense::class, 'tenant_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(HQSubscription::class, 'tenant_id');
    }

    public function releaseChannel()
    {
        return $this->belongsTo(HQReleaseChannel::class, 'hq_release_channel_id');
    }

    public function instanceGroup()
    {
        return $this->belongsTo(HQInstanceGroup::class, 'hq_instance_group_id');
    }

    public function maintenanceWindows()
    {
        return $this->morphMany(HQMaintenanceWindow::class, 'targetable');
    }
}
