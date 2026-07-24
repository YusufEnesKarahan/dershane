<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\Admission\EnrollStudentDTO;
use App\DTOs\Admission\UploadDocumentDTO;
use App\Domain\Admission\Actions\CompleteEnrollment;
use App\Domain\Admission\Actions\UploadDocument;
use App\Domain\Admission\Actions\ApproveDocument;
use App\Domain\Admission\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct(protected EnrollmentService $enrollmentService) {}

    public function complete(Request $request, CompleteEnrollment $action)
    {
        $request->validate([
            'student_admission_id' => 'required|exists:student_admissions,id',
        ]);

        $dto = EnrollStudentDTO::fromArray($request->all());
        $enrollment = $action->execute($dto, Auth::id());

        return redirect()->back()->with('success', 'Kesin kayıt başarıyla tamamlandı, öğrenci kartı ve faturası oluşturuldu.');
    }

    public function uploadDocument(Request $request, UploadDocument $action)
    {
        $request->validate([
            'student_admission_id' => 'required|exists:student_admissions,id',
            'document_type' => 'required|string',
            'file_name' => 'required|string',
        ]);

        $filePath = $request->file_path ?? ('documents/adm_' . $request->student_admission_id . '_' . time() . '.pdf');

        $dto = new UploadDocumentDTO(
            (int) $request->student_admission_id,
            $request->document_type,
            $request->file_name,
            $filePath,
            Auth::id()
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Kayıt belgesi başarıyla yüklendi.');
    }

    public function approveDocument(Request $request, $id, ApproveDocument $action)
    {
        $action->execute((int) $id, Auth::id());
        return redirect()->back()->with('success', 'Kayıt belgesi onaylandı.');
    }
}
