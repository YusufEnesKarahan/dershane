<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    public function weeks()
    {
        return $this->hasMany(AcademicWeek::class);
    }
}
