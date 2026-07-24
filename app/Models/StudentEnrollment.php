<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEnrollment extends Model
{
    protected $fillable = [
        'student_admission_id',
        'student_id',
        'branch_id',
        'classroom_id',
        'invoice_id',
        'enrollment_no',
        'enrollment_date',
        'academic_year',
        'final_fee',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'final_fee' => 'decimal:2',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
