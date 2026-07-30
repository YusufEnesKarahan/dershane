<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQInstanceGroup extends Model
{
    use HasFactory;

    protected $table = 'hq_instance_groups';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function tenants()
    {
        return $this->hasMany(HQTenant::class, 'hq_instance_group_id');
    }

    public function maintenanceWindows()
    {
        return $this->morphMany(HQMaintenanceWindow::class, 'targetable');
    }
}
