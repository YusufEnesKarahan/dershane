<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'slug', 'description', 'course_level_id', 
        'duration', 'capacity', 'status', 'is_active', 'cover_image',
        'seo_title', 'seo_description', 'seo_keywords'
    ];

    public function level()
    {
        return $this->belongsTo(CourseLevel::class, 'course_level_id');
    }

    public function subjects()
    {
        return $this->hasMany(CourseSubject::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function pricings()
    {
        return $this->hasMany(CoursePricing::class);
    }

    public function currentPricing()
    {
        return $this->hasOne(CoursePricing::class)->latestOfMany();
    }

    public function prerequisites()
    {
        return $this->belongsToMany(self::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'course_teachers');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'course_branches');
    }
}
