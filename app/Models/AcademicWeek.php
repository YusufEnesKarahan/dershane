<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicWeek extends Model
{
    protected $fillable = ['academic_term_id', 'week_number', 'start_date', 'end_date'];
}
