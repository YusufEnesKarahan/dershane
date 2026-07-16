<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogView extends Model
{
    protected $fillable = ['blog_id', 'ip_address', 'user_agent', 'is_unique'];
}
