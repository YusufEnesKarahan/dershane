<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentAdmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_id',
        'branch_id',
        'advisor_id',
        'admission_no',
        'first_name',
        'last_name',
        'tc_no',
        'phone',
        'email',
        'guardian_name',
        'guardian_phone',
        'guardian_tc_no',
        'school',
        'grade',
        'program',
        'total_amount',
        'deposit_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AdmissionDocument::class, 'student_admission_id');
    }

    public function admissionNotes(): HasMany
    {
        return $this->hasMany(AdmissionNote::class, 'student_admission_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(AdmissionStatusLog::class, 'student_admission_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EnrollmentContract::class, 'student_admission_id');
    }

    public function enrollment(): HasOne
    {
        return $this->hasOne(StudentEnrollment::class, 'student_admission_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(RegistrationPayment::class, 'student_admission_id');
    }
}
