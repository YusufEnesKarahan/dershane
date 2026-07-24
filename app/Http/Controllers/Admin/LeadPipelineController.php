<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\CRM\Services\LeadPipelineService;
use App\Domain\CRM\Actions\ConvertLead;
use App\Domain\CRM\Actions\CloseLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadPipelineController extends Controller
{
    public function __construct(protected LeadPipelineService $pipelineService) {}

    public function index()
    {
        $board = $this->pipelineService->getPipelineBoard();
        return view('admin.crm.pipeline', compact('board'));
    }

    public function move(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:crm_leads,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
        ]);

        $this->pipelineService->moveLead(
            (int) $request->lead_id,
            (int) $request->lead_status_id,
            Auth::id()
        );

        return redirect()->back()->with('success', 'Aday durumu güncellendi.');
    }

    public function convert(Request $request, $id, ConvertLead $action)
    {
        $action->execute((int) $id, Auth::id());
        return redirect()->back()->with('success', 'Lead başarıyla öğrenci kaydına dönüştürüldü.');
    }

    public function close(Request $request, $id, CloseLead $action)
    {
        $action->execute((int) $id, Auth::id());
        return redirect()->back()->with('success', 'Lead kaybedildi olarak işaretlendi.');
    }
}
