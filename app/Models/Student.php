<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_number', 'identity_number', 'first_name', 'last_name',
        'birth_date', 'gender', 'photo', 'branch_id', 'classroom_id', 'status'
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function guardians()
    {
        return $this->hasMany(StudentGuardian::class);
    }

    public function primaryGuardian()
    {
        return $this->hasOne(StudentGuardian::class)->where('is_primary', true);
    }

    public function contact()
    {
        return $this->hasOne(StudentContact::class);
    }

    public function address()
    {
        return $this->hasOne(StudentAddress::class);
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_enrollments');
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function transfers()
    {
        return $this->hasMany(StudentTransfer::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(StudentStatusHistory::class);
    }
}
