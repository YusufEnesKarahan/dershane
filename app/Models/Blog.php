<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category_id',
        'author_id',
        'status',
        'published_at',
        'featured_image',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'robots',
        'reading_time',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag_post');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function revisions()
    {
        return $this->hasMany(BlogRevision::class);
    }

    public function views()
    {
        return $this->hasMany(BlogView::class);
    }

    public function reactions()
    {
        return $this->hasMany(BlogReaction::class);
    }

    public function relatedPosts()
    {
        return $this->belongsToMany(self::class, 'blog_related_posts', 'blog_id', 'related_id');
    }
}
