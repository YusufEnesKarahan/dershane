<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeworkComment extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'homework_id',
        'user_id',
        'comment',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
