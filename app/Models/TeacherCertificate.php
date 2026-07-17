<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherCertificate extends Model
{
    protected $fillable = ['teacher_id', 'name', 'issuing_organization', 'issue_date', 'expiry_date'];
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
