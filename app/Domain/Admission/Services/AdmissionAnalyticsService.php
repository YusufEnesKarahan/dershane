<?php

namespace App\Domain\Admission\Services;

use App\Models\StudentAdmission;
use App\Models\StudentEnrollment;
use App\Models\AdmissionDocument;
use Illuminate\Support\Facades\DB;

class AdmissionAnalyticsService
{
    public function getSummaryMetrics(): array
    {
        $totalAdmissions = StudentAdmission::count();
        $totalPreRegistration = StudentAdmission::where('status', 'pre_registration')->count();
        $totalPendingDocuments = AdmissionDocument::where('status', 'pending')->count();
        $totalEnrolled = StudentAdmission::whereIn('status', ['enrolled', 'active_student'])->count();

        $conversionRate = $totalAdmissions > 0 ? round(($totalEnrolled / $totalAdmissions) * 100, 1) : 0.0;

        $statusDistribution = StudentAdmission::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $totalDepositCollected = StudentAdmission::sum('deposit_amount');

        return [
            'total_admissions' => $totalAdmissions,
            'total_pre_registration' => $totalPreRegistration,
            'total_pending_documents' => $totalPendingDocuments,
            'total_enrolled' => $totalEnrolled,
            'conversion_rate' => $conversionRate,
            'status_distribution' => $statusDistribution,
            'total_deposit_collected' => $totalDepositCollected,
        ];
    }
}
