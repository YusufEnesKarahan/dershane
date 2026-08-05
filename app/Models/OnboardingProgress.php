<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class OnboardingProgress extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'onboarding_progress';

    protected $fillable = [
        'branch_id',
        'company_info_completed',
        'first_branch_completed',
        'teacher_added',
        'student_added',
        'course_created',
        'exam_created',
    ];

    protected $casts = [
        'company_info_completed' => 'boolean',
        'first_branch_completed' => 'boolean',
        'teacher_added' => 'boolean',
        'student_added' => 'boolean',
        'course_created' => 'boolean',
        'exam_created' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
