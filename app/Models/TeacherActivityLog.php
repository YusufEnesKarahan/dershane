<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['teacher_id', 'action', 'details'];
}
