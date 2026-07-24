<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'email_enabled', 'panel_enabled', 'sms_enabled'];

    protected function casts(): array
    {
        return ['email_enabled' => 'boolean', 'panel_enabled' => 'boolean', 'sms_enabled' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
