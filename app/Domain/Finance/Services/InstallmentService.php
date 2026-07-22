<?php

namespace App\Domain\Finance\Services;

use App\Models\PaymentPlan;

class InstallmentService
{
    public function createPlan(int $studentId, int $installments, float $totalAmount, string $startDate): PaymentPlan
    {
        $installmentAmount = round($totalAmount / max(1, $installments), 2);
        return PaymentPlan::create([
            'student_id' => $studentId,
            'total_installments' => $installments,
            'installment_amount' => $installmentAmount,
            'start_date' => $startDate,
        ]);
    }
}
