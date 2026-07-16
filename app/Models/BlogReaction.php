<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogReaction extends Model
{
    protected $fillable = ['blog_id', 'user_id', 'type'];
}
