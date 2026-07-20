<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceExcuse extends Model
{
    protected $fillable = [
        'attendance_id', 'student_id', 'excuse_reason', 'document_path', 'status', 'approved_by'
    ];
}
