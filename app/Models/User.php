<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function roles() {
        return $this->belongsToMany(Role::class);
    }
    public function teacher() {
        return $this->hasOne(Teacher::class);
    }

    public function hasRole(string|array $roles): bool
    {
        return app(\App\Domain\Auth\Services\AuthorizationService::class)->hasRole($this, $roles);
    }

    public function hasPermission(string $permission): bool
    {
        return app(\App\Domain\Auth\Services\AuthorizationService::class)->hasPermission($this, $permission);
    }

}