<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'exam_id',
        'student_id',
        'score',
        'total_net',
        'rank',
        'percentile',
        'correct_answers',
        'wrong_answers',
        'empty_answers',
        'is_absent',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'total_net' => 'decimal:2',
        'percentile' => 'decimal:2',
        'is_absent' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function branchResults(): HasMany
    {
        return $this->hasMany(ExamBranchResult::class);
    }
}
