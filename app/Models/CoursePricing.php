<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePricing extends Model
{
    protected $fillable = ['course_id', 'price', 'currency', 'installment_options'];
}
