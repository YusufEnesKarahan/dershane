<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedPost extends Model
{
    protected $table = 'blog_related_posts';
    protected $fillable = ['blog_id', 'related_id'];
}
