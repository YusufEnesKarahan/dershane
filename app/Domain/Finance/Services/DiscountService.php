<?php

namespace App\Domain\Finance\Services;

use App\Models\Discount;

class DiscountService
{
    public function applyDiscount(float $amount, Discount $discount): float
    {
        if ($discount->type === 'Percentage') {
            return round($amount * (1 - ($discount->value / 100)), 2);
        }
        return max(0, $amount - $discount->value);
    }
}
