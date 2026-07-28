<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdatePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'version',
        'build',
        'description',
        'checksum',
        'is_mandatory',
        'release_date',
        'metadata',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'release_date' => 'datetime',
        'metadata' => 'array',
    ];
}
