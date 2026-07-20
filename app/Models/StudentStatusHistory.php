<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentStatusHistory extends Model
{
    protected $fillable = ['student_id', 'old_status', 'new_status', 'reason', 'changed_by'];
}
