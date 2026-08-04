<?php

namespace App\Domain\Package\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $table = 'features';

    protected $fillable = [
        'name',
        'code',
        'description',
        'module',
        'status',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_features', 'feature_id', 'package_id');
    }
}
