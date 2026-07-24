<?php

namespace App\Domain\CRM\Services;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class LeadAnalyticsService
{
    public function getAnalyticsSummary(): array
    {
        $totalLeads = Lead::count();
        
        $registeredStatusId = LeadStatus::where('code', 'REGISTERED')->value('id');
        $registeredLeads = $registeredStatusId ? Lead::where('lead_status_id', $registeredStatusId)->count() : 0;
        
        $conversionRate = $totalLeads > 0 ? round(($registeredLeads / $totalLeads) * 100, 1) : 0.0;

        // Advisor Performance
        $advisorPerformance = DB::table('crm_leads')
            ->join('users', 'crm_leads.advisor_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(crm_leads.id) as total_assigned'), DB::raw("sum(case when crm_leads.lead_status_id = {$registeredStatusId} then 1 else 0 end) as total_registered"))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_registered', 'desc')
            ->get();

        // Lead Status Funnel
        $statusDistribution = DB::table('crm_leads')
            ->join('lead_statuses', 'crm_leads.lead_status_id', '=', 'lead_statuses.id')
            ->select('lead_statuses.name', 'lead_statuses.color', DB::raw('count(crm_leads.id) as total'))
            ->groupBy('lead_statuses.id', 'lead_statuses.name', 'lead_statuses.color')
            ->orderBy('lead_statuses.sort_order', 'asc')
            ->get();

        // Lead Source Distribution
        $sourceDistribution = DB::table('crm_leads')
            ->join('lead_sources', 'crm_leads.lead_source_id', '=', 'lead_sources.id')
            ->select('lead_sources.name', DB::raw('count(crm_leads.id) as total'))
            ->groupBy('lead_sources.id', 'lead_sources.name')
            ->get();

        // Branch Performance
        $branchPerformance = DB::table('crm_leads')
            ->join('branches', 'crm_leads.branch_id', '=', 'branches.id')
            ->select('branches.name', DB::raw('count(crm_leads.id) as total_leads'), DB::raw("sum(case when crm_leads.lead_status_id = {$registeredStatusId} then 1 else 0 end) as converted_leads"))
            ->groupBy('branches.id', 'branches.name')
            ->get();

        return [
            'total_leads' => $totalLeads,
            'registered_leads' => $registeredLeads,
            'conversion_rate' => $conversionRate,
            'advisor_performance' => $advisorPerformance,
            'status_distribution' => $statusDistribution,
            'source_distribution' => $sourceDistribution,
            'branch_performance' => $branchPerformance,
        ];
    }
}
