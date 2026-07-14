<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    /** @use HasFactory<\Database\Factories\BlogFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'author_id',
        'image_path',
        'is_published',
        'published_at'
    ];



    public function category() {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }

}