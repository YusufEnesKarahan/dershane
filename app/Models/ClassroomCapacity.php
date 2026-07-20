<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomCapacity extends Model
{
    protected $fillable = ['classroom_id', 'max_capacity', 'exam_capacity', 'effective_date'];
}
