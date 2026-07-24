<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionNote extends Model
{
    protected $fillable = [
        'student_admission_id',
        'user_id',
        'note_text',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
