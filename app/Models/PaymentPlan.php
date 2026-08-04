<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;

class PaymentPlan extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'student_id',
        'academic_term_id',
        'title',
        'total_amount',
        'discount_amount',
        'net_amount',
        'currency',
        'status'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
