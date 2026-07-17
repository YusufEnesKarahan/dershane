<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherNote extends Model
{
    protected $fillable = ['teacher_id', 'author_id', 'note'];
}
