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

#[Fillable(['name', 'email', 'password', 'phone', 'avatar', 'status', 'last_login_at', 'branch_id', 'preferences'])]
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
            'status' => \App\Enums\UserStatus::class,
            'preferences' => 'array',
            'last_login_at' => 'datetime',
        ];
    }


    public function roles() {
        return $this->belongsToMany(Role::class);
    }
    public function teacher() {
        return $this->hasOne(Teacher::class);
    }
    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function hasRole(string|array $roles): bool
    {
        return app(\App\Domain\Auth\Services\AuthorizationService::class)->hasRole($this, $roles);
    }

    public function hasPermission(string $permission): bool
    {
        return app(\App\Domain\Auth\Services\PermissionResolver::class)->hasPermission($this, $permission);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === \App\Enums\UserStatus::ACTIVE;
    }

    public function isAdministrator(): bool
    {
        return $this->hasRole('Administrator');
    }

    public function getAvatarUrl(): string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff';
    }

    public function displayName(): string
    {
        return $this->name;
    }

    public function initials(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= substr($word, 0, 1);
        }
        return strtoupper(substr($initials, 0, 2));
    }

    public function preferredLanguage(): string
    {
        return $this->preferences['language'] ?? 'tr';
    }

    public function preferredTheme(): string
    {
        return $this->preferences['theme'] ?? 'light';
    }

    public function preferredTimezone(): string
    {
        return $this->preferences['timezone'] ?? 'Europe/Istanbul';
    }
}
