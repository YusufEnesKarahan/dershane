<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['name', 'code', 'max_days', 'description'];

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
