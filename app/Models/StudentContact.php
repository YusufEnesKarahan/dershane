<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentContact extends Model
{
    protected $fillable = ['student_id', 'phone', 'email', 'emergency_contact', 'emergency_phone'];
}
