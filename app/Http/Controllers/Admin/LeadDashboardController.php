<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\CRM\Services\LeadAnalyticsService;
use App\Domain\CRM\Services\FollowupService;
use App\DTOs\CRM\CreateFollowupDTO;
use App\Domain\CRM\Actions\ScheduleFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadDashboardController extends Controller
{
    public function __construct(
        protected LeadAnalyticsService $analyticsService,
        protected FollowupService $followupService
    ) {}

    public function index()
    {
        $analytics = $this->analyticsService->getAnalyticsSummary();
        $followups = $this->followupService->getFollowups();

        // Calculate count of today's calls and pending followups
        $todayStr = date('Y-m-d');
        $todayCalls = $followups->filter(function($f) use ($todayStr) {
            return $f->followup_date->format('Y-m-d') === $todayStr;
        })->count();

        $pendingLeadsCount = $analytics['total_leads'] - $analytics['registered_leads'];

        return view('admin.crm.dashboard', compact('analytics', 'followups', 'todayCalls', 'pendingLeadsCount'));
    }

    public function followups()
    {
        $followups = $this->followupService->getFollowups();
        $leads = \App\Models\Lead::all();
        return view('admin.crm.followups', compact('followups', 'leads'));
    }

    public function storeFollowup(Request $request, ScheduleFollowup $action)
    {
        $request->validate([
            'lead_id' => 'required|exists:crm_leads,id',
            'followup_date' => 'required',
            'reminder_note' => 'required|string',
        ]);

        $dto = new CreateFollowupDTO(
            (int) $request->lead_id,
            $request->followup_date,
            $request->reminder_note,
            $request->priority ?? 'Medium',
            Auth::id()
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Takip görevi başarıyla eklendi.');
    }

    public function completeFollowup($id)
    {
        $this->followupService->completeFollowup((int) $id, Auth::id());
        return redirect()->back()->with('success', 'Takip arama görevi tamamlandı olarak işaretlendi.');
    }
}
