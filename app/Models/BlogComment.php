<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use SoftDeletes;

    protected $fillable = ['blog_id', 'parent_id', 'user_id', 'author_name', 'author_email', 'content', 'status', 'reports_count'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 'Approved')->orderBy('created_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
