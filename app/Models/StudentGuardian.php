<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    protected $fillable = ['student_id', 'guardian_name', 'relation', 'phone', 'email', 'is_primary', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
