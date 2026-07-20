<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentNote extends Model
{
    protected $fillable = ['student_id', 'author_id', 'note'];
}
