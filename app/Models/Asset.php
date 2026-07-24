<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'category_id', 'asset_code', 'name', 'brand', 'model', 'serial_number',
        'purchase_date', 'purchase_price', 'warranty_end_date', 'status',
        'location_id', 'description'
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function transfers()
    {
        return $this->hasMany(AssetTransfer::class);
    }
}
