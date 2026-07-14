<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name'
    ];



    public function roles() {
        return $this->belongsToMany(Role::class);
    }

}