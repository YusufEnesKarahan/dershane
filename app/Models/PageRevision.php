<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageRevision extends Model
{
    protected $fillable = [
        'page_id',
        'revision_no',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_snapshot',
        'content_snapshot',
        'author_id',
    ];

    protected $casts = [
        'seo_snapshot' => 'array',
        'content_snapshot' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
