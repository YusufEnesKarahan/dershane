<?php

namespace App\Domain\Finance\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\StudentDebt;

class FinanceAnalyticsService
{
    public function getSummary(): array
    {
        $totalInvoiced = Invoice::where('status', '!=', 'Cancelled')->sum('total_amount');
        $totalCollected = Payment::where('status', 'Completed')->sum('amount');
        $totalPendingDebt = StudentDebt::where('status', '!=', 'Paid')->sum('remaining_amount');
        $collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 1) : 0;

        return [
            'total_invoiced' => round($totalInvoiced, 2),
            'total_collected' => round($totalCollected, 2),
            'total_pending_debt' => round($totalPendingDebt, 2),
            'collection_rate' => $collectionRate,
        ];
    }
}
