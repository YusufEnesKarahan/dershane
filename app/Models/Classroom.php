<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    /** @use HasFactory<\Database\Factories\ClassroomFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'branch_id',
        'capacity'
    ];



    public function branch() {
        return $this->belongsTo(Branch::class);
    }

}