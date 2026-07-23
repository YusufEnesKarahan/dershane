<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    protected $fillable = ['report_type', 'format', 'file_path', 'status', 'requested_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
