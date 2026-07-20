<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'branch_id', 'classroom_type_id', 'capacity', 'color_code', 'is_active'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function type()
    {
        return $this->belongsTo(ClassroomType::class, 'classroom_type_id');
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }
}
