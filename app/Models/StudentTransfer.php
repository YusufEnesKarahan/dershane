<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransfer extends Model
{
    protected $fillable = [
        'student_id', 'from_branch_id', 'to_branch_id',
        'from_classroom_id', 'to_classroom_id', 'reason', 'transfer_date'
    ];
}
