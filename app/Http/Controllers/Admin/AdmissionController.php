<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAdmission;
use App\Models\Branch;
use App\Models\User;
use App\Models\Lead;
use App\Models\Classroom;
use App\DTOs\Admission\CreateAdmissionDTO;
use App\DTOs\Admission\UpdateAdmissionDTO;
use App\Domain\Admission\Actions\CreateAdmission;
use App\Domain\Admission\Actions\ConvertLeadToAdmission;
use App\Domain\Admission\Services\AdmissionService;
use App\Domain\Admission\Services\AdmissionAnalyticsService;
use App\Domain\Admission\Services\AdmissionDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdmissionController extends Controller
{
    public function __construct(
        protected AdmissionService $admissionService,
        protected AdmissionAnalyticsService $analyticsService,
        protected AdmissionDocumentService $documentService
    ) {}

    public function dashboard()
    {
        $analytics = $this->analyticsService->getSummaryMetrics();
        $recentAdmissions = $this->admissionService->getAdmissions()->take(5);
        return view('admin.admission.dashboard', compact('analytics', 'recentAdmissions'));
    }

    public function index()
    {
        $admissions = $this->admissionService->getAdmissions();
        $branches = Branch::all();
        $advisors = User::all();
        $leads = Lead::whereNotIn('id', $admissions->pluck('lead_id')->filter())->get();

        return view('admin.admission.index', compact('admissions', 'branches', 'advisors', 'leads'));
    }

    public function workflow()
    {
        $admissions = $this->admissionService->getAdmissions();
        return view('admin.admission.workflow', compact('admissions'));
    }

    public function documents()
    {
        $admissions = $this->admissionService->getAdmissions();
        return view('admin.admission.documents', compact('admissions'));
    }

    public function store(Request $request, CreateAdmission $action)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
        ]);

        $dto = CreateAdmissionDTO::fromArray($request->all());
        $action->execute($dto);

        return redirect()->back()->with('success', 'Ön kayıt başvurusu başarıyla oluşturuldu.');
    }

    public function convertLead(Request $request, $leadId, ConvertLeadToAdmission $action)
    {
        $admission = $action->execute((int) $leadId, Auth::id());
        return redirect()->route('admin.admission.show', $admission->id)->with('success', 'CRM Lead kaydı Ön Kayıt sistemine dönüştürüldü.');
    }

    public function show($id)
    {
        $admission = $this->admissionService->findAdmission((int) $id);
        if (!$admission) {
            return redirect()->route('admin.admission.index')->with('error', 'Kayıt bulunamadı.');
        }

        $classrooms = Classroom::all();

        return view('admin.admission.show', compact('admission', 'classrooms'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $this->admissionService->updateStatus(
            (int) $id,
            $request->status,
            $request->description ?? 'Aşama durumu güncellendi.',
            Auth::id()
        );

        return redirect()->back()->with('success', 'Kayıt aşaması güncellendi.');
    }

    public function storeNote(Request $request, $id)
    {
        $request->validate([
            'note_text' => 'required|string',
        ]);

        $this->admissionService->addNote((int) $id, $request->note_text, Auth::id());

        return redirect()->back()->with('success', 'Görüşme notu eklendi.');
    }
}
