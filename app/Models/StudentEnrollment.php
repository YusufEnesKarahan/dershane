<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $fillable = [
        'student_id', 'course_id', 'academic_term_id', 'status', 'price_paid', 'enrollment_date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }
}
