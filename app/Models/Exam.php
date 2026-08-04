<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'academic_term_id',
        'title',
        'description',
        'type', // mock_exam, practice_exam, final_exam, quiz
        'exam_date',
        'duration_minutes',
        'total_score',
        'status', // draft, published, completed, cancelled
        'created_by',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'total_score' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(ExamRanking::class);
    }
}
