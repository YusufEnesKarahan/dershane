<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\TenantScoped;

class ParentMeeting extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'branch_id',
        'student_id',
        'guardian_id',
        'teacher_id',
        'meeting_date',
        'summary',
        'decisions'
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(StudentGuardian::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
