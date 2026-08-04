<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeworkFile extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'homework_files';

    protected $fillable = [
        'branch_id',
        'homework_id',
        'homework_submission_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function submission()
    {
        return $this->belongsTo(HomeworkSubmission::class, 'homework_submission_id');
    }
}
