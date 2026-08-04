<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationTemplate extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'name',
        'type',
        'title_template',
        'message_template',
        'slug',
        'code',
        'title',
        'body',
        'channel',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
