<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationPayment extends Model
{
    protected $fillable = [
        'student_admission_id',
        'amount',
        'payment_method',
        'reference_no',
        'payment_date',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
