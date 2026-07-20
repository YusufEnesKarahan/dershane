<?php
namespace App\Domain\Course\Services;

use App\Models\Course;
use App\Models\CoursePricing;

class CoursePricingService
{
    public function setPrice(Course $course, float $price, string $currency = 'TRY', int $installments = 1): CoursePricing
    {
        return CoursePricing::create([
            'course_id' => $course->id,
            'price' => $price,
            'currency' => $currency,
            'installment_options' => $installments,
        ]);
    }
}
