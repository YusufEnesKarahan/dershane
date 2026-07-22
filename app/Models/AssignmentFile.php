<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentFile extends Model
{
    protected $fillable = ['assignment_id', 'submission_id', 'title', 'file_path', 'file_type', 'size_bytes'];
}
