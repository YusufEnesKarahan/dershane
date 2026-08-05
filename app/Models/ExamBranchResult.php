<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamBranchResult extends Model
{
    protected $fillable = [
        'exam_result_id',
        'branch_name',
        'correct_count',
        'wrong_count',
        'empty_count',
        'net_count',
    ];

    protected $casts = [
        'net_count' => 'decimal:2',
    ];

    public function examResult(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class);
    }
}
