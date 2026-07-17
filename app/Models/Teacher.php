<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'branch_id', 'title', 'bio', 'specialties', 'education', 'experience_years', 'emergency_contact', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function documents()
    {
        return $this->hasMany(TeacherDocument::class);
    }

    public function certificates()
    {
        return $this->hasMany(TeacherCertificate::class);
    }

    public function experiences()
    {
        return $this->hasMany(TeacherExperience::class);
    }

    public function availabilities()
    {
        return $this->hasMany(TeacherAvailability::class);
    }

    public function schedules()
    {
        return $this->hasMany(TeacherSchedule::class);
    }

    public function notes()
    {
        return $this->hasMany(TeacherNote::class);
    }

    public function performances()
    {
        return $this->hasMany(TeacherPerformance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(TeacherLeaveRequest::class);
    }

    public function salaryProfile()
    {
        return $this->hasOne(TeacherSalaryProfile::class);
    }

    public function contracts()
    {
        return $this->hasMany(TeacherContract::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(TeacherActivityLog::class);
    }
}
