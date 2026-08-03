<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['branch_id', 'title', 'content', 'type', 'target_role', 'created_by', 'published_at', 'status'])]
class Announcement extends Model
{
    use TenantScoped, SoftDeletes;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }
}
