<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    protected $fillable = ['student_id', 'guardian_name', 'relation', 'phone', 'email', 'is_primary'];
}
