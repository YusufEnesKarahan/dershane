<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementRead extends Model
{
    protected $fillable = ['announcement_id', 'user_id', 'read_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
