<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'invoice_number',
        'student_id',
        'guardian_id',
        'issue_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'status', // Pending, Partial, Paid, Cancelled (Bekliyor, Kısmi Ödendi, Ödendi, İptal)
        'description',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function debt()
    {
        return $this->hasOne(StudentDebt::class);
    }
}
