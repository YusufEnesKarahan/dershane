<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'address',
        'manager_id'
    ];



    public function manager() {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function teachers() {
        return $this->hasMany(Teacher::class);
    }
    public function courses() {
        return $this->hasMany(Course::class);
    }

}