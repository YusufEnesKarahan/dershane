<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id', 'student_id', 'submission_date', 'is_late', 'remarks', 'status'
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function files()
    {
        return $this->hasMany(AssignmentFile::class, 'submission_id');
    }

    public function comments()
    {
        return $this->hasMany(AssignmentComment::class, 'submission_id');
    }

    public function score()
    {
        return $this->hasOne(AssignmentScore::class, 'submission_id');
    }
}
