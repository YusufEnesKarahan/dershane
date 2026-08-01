<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InstitutionRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'institution_registrations';

    protected $fillable = [
        'uuid',
        'institution_id',
        'current_step',
        'status',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }
}
