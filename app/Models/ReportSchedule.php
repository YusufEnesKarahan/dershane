<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSchedule extends Model
{
    protected $fillable = ['report_type', 'format', 'email_recipients', 'cron_expression', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
