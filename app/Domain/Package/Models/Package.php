<?php

namespace App\Domain\Package\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'name',
        'code',
        'description',
        'price_yearly',
        'price_3_year',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_yearly' => 'decimal:2',
            'price_3_year' => 'decimal:2',
        ];
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'package_features', 'package_id', 'feature_id');
    }

    public function branchPackages()
    {
        return $this->hasMany(BranchPackage::class, 'package_id');
    }

    public function hasFeature(string $featureCode): bool
    {
        return $this->features->contains('code', $featureCode);
    }
}
