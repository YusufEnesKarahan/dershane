<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Branch;
use App\Models\User;
use App\DTOs\CRM\CreateLeadDTO;
use App\DTOs\CRM\UpdateLeadDTO;
use App\DTOs\CRM\LeadNoteDTO;
use App\DTOs\CRM\AssignLeadDTO;
use App\Domain\CRM\Actions\CreateLead;
use App\Domain\CRM\Actions\UpdateLead;
use App\Domain\CRM\Actions\AddLeadNote;
use App\Domain\CRM\Actions\AssignLead;
use App\Domain\CRM\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct(protected LeadService $leadService) {}

    public function index()
    {
        $leads = $this->leadService->getLeads();
        $sources = LeadSource::all();
        $statuses = LeadStatus::all();
        $branches = Branch::all();
        $advisors = User::all();

        return view('admin.crm.index', compact('leads', 'sources', 'statuses', 'branches', 'advisors'));
    }

    public function store(Request $request, CreateLead $action)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
        ]);

        $dto = CreateLeadDTO::fromArray($request->all());
        $action->execute($dto);

        return redirect()->back()->with('success', 'Lead kaydı başarıyla oluşturuldu.');
    }

    public function show($id)
    {
        $lead = $this->leadService->findLead((int) $id);
        if (!$lead) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead kaydı bulunamadı.');
        }

        $advisors = User::all();
        $branches = Branch::all();

        return view('admin.crm.show', compact('lead', 'advisors', 'branches'));
    }

    public function update(Request $request, $id, UpdateLead $action)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
        ]);

        $dto = UpdateLeadDTO::fromArray($request->all());
        $action->execute((int) $id, $dto);

        return redirect()->back()->with('success', 'Lead kaydı güncellendi.');
    }

    public function storeNote(Request $request, $id, AddLeadNote $action)
    {
        $request->validate([
            'note_text' => 'required|string',
        ]);

        $dto = new LeadNoteDTO(
            (int) $id,
            $request->note_text,
            Auth::id()
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Görüşme notu eklendi.');
    }

    public function assign(Request $request, $id, AssignLead $action)
    {
        $dto = new AssignLeadDTO(
            (int) $id,
            $request->advisor_id ? (int) $request->advisor_id : null,
            $request->branch_id ? (int) $request->branch_id : null,
            Auth::id()
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Lead atama bilgileri güncellendi.');
    }
}
