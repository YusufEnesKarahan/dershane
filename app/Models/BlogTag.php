<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    protected $fillable = ['name', 'slug', 'usage_count'];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_tag_post');
    }
}
