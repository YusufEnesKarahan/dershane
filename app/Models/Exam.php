<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;

class Exam extends Model
{
    use TenantScoped;

    protected $fillable = [
        'branch_id', 'classroom_id', 'title', 'description', 'exam_type_id', 
        'exam_date', 'duration_minutes', 'total_score', 'status', 'created_by'
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function type()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
