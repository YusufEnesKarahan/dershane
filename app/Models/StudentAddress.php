<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAddress extends Model
{
    protected $fillable = ['student_id', 'city', 'district', 'address_text'];
}
