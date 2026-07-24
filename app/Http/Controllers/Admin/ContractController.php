<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\Admission\CreateContractDTO;
use App\Domain\Admission\Actions\GenerateContract;
use App\Domain\Admission\Services\ContractService;
use App\Models\EnrollmentContract;
use App\Models\StudentAdmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function index()
    {
        $contracts = EnrollmentContract::with(['admission', 'template'])->orderBy('created_at', 'desc')->get();
        $templates = $this->contractService->getTemplates();
        $admissions = StudentAdmission::all();

        return view('admin.admission.contracts', compact('contracts', 'templates', 'admissions'));
    }

    public function generate(Request $request, GenerateContract $action)
    {
        $request->validate([
            'student_admission_id' => 'required|exists:student_admissions,id',
            'contract_template_id' => 'required|exists:contract_templates,id',
        ]);

        $dto = CreateContractDTO::fromArray($request->all());
        $action->execute($dto, Auth::id());

        return redirect()->back()->with('success', 'Dinamik verili kayıt sözleşmesi başarıyla üretildi.');
    }

    public function sign(Request $request, $id)
    {
        $this->contractService->signContract((int) $id, Auth::id());
        return redirect()->back()->with('success', 'Sözleşme imzalandı olarak işaretlendi.');
    }
}
