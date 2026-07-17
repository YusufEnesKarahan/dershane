<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherDocument extends Model
{
    protected $fillable = ['teacher_id', 'title', 'type', 'file_path'];
}
