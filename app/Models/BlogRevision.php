<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogRevision extends Model
{
    protected $fillable = ['blog_id', 'revision_no', 'title', 'slug', 'content', 'excerpt', 'seo_snapshot', 'author_id'];

    protected $casts = [
        'seo_snapshot' => 'array',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
