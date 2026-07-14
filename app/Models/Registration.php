<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_name',
        'student_phone',
        'parent_name',
        'parent_phone',
        'grade',
        'program',
        'branch_id',
        'status'
    ];



    public function branch() {
        return $this->belongsTo(Branch::class);
    }

}