<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'color',
    ];

    public function users() {
        return $this->belongsToMany(User::class);
    }
    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }

    public function isSystemRole(): bool
    {
        return app(\App\Domain\Auth\Services\SystemRoleGuard::class)->isProtected($this);
    }
}