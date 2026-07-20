<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];
}
