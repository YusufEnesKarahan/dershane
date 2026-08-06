<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreRegistration extends Model
{
    use HasFactory, TenantScoped, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'student_name',
        'phone',
        'email',
        'classroom_name',
        'interested_program',
        'source',
        'status',
        'assigned_to',
        'notes',
        'reminder_at',
        'converted_student_id',
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'converted_student_id');
    }
}
