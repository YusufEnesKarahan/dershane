<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes, TenantScoped;

    protected $fillable = [
        'code', 'name', 'branch_id', 'teacher_id', 'classroom_type_id', 'capacity', 'color_code', 'is_active'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function type()
    {
        return $this->belongsTo(ClassroomType::class, 'classroom_type_id');
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'classroom_student');
    }
}
