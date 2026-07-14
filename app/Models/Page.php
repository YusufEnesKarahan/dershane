<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'template',
        'status',
        'published_at',
        'author_id',
        'parent_id',
        'sort_order',
        'is_homepage',
        'is_system',
        'revisions'
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'is_system' => 'boolean',
        'published_at' => 'datetime',
        'revisions' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isSystemPage(): bool
    {
        return (bool) $this->is_system;
    }

    public function isHomepage(): bool
    {
        return (bool) $this->is_homepage;
    }
}
