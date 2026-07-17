<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSalaryProfile extends Model
{
    protected $fillable = ['teacher_id', 'base_salary', 'payment_type', 'bonus', 'deductions'];
}
