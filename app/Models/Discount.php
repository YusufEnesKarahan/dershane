<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class Discount extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'student_id',
        'title',
        'type',
        'value',
        'reason',
        'approved_by'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
