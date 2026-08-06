<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory, TenantScoped, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'cover_image',
        'type',
        'target_role',
        'created_by',
        'published_at',
        'publish_at',
        'expire_at',
        'is_pinned',
        'is_popup',
        'is_all_branches',
        'notify_roles',
        'status', // Draft, Scheduled, Published, Archived
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
        'is_pinned' => 'boolean',
        'is_popup' => 'boolean',
        'is_all_branches' => 'boolean',
        'notify_roles' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($announcement) {
            if (empty($announcement->slug)) {
                $announcement->slug = Str::slug($announcement->title) . '-' . Str::random(5);
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AnnouncementCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'announcement_branches');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereIn('status', ['Published', 'published'])
            ->where(function ($q) {
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expire_at')->orWhere('expire_at', '>=', now());
            });
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->orderBy('is_pinned', 'desc');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expire_at')->orWhere('expire_at', '>=', now());
        });
    }

    public function scopePopup(Builder $query): Builder
    {
        return $query->where('is_popup', true);
    }
}
