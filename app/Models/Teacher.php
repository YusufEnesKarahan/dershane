<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'title',
        'bio',
        'specialities'
    ];



    public function user() {
        return $this->belongsTo(User::class);
    }
    public function branch() {
        return $this->belongsTo(Branch::class);
    }

}