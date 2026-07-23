<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Reporting\Services\ReportingService;
use App\DTOs\Reporting\ReportScheduleDTO;
use App\DTOs\Reporting\ReportExportDTO;
use App\Domain\Reporting\Actions\ScheduleReport;
use App\Domain\Reporting\Actions\ExportReport;
use App\Models\ReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(protected ReportingService $reportingService) {}

    public function index()
    {
        $schedules = $this->reportingService->getSchedules();
        $exports = $this->reportingService->getExports();
        $executiveReports = $this->reportingService->getExecutiveReports();

        return view('admin.reporting.reports', compact('schedules', 'exports', 'executiveReports'));
    }

    public function storeSchedule(Request $request, ScheduleReport $action)
    {
        $request->validate([
            'report_type' => 'required|string',
            'format' => 'required|string|in:PDF,Excel,CSV',
            'email_recipients' => 'required|string',
            'cron_expression' => 'required|string',
        ]);

        $dto = new ReportScheduleDTO(
            $request->report_type,
            $request->format,
            $request->email_recipients,
            $request->cron_expression
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Rapor planı başarıyla oluşturuldu.');
    }

    public function export(Request $request, ExportReport $action)
    {
        $request->validate([
            'report_type' => 'required|string',
            'format' => 'required|string|in:PDF,Excel,CSV',
        ]);

        $dto = new ReportExportDTO(
            $request->report_type,
            $request->format,
            Auth::id()
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Rapor dışa aktarımı başarıyla başlatıldı ve tamamlandı.');
    }

    public function download($id)
    {
        $export = ReportExport::findOrFail($id);

        if (!$export->file_path || !file_exists($export->file_path)) {
            return redirect()->back()->with('error', 'Dosya bulunamadı veya henüz oluşturulmadı.');
        }

        return response()->download($export->file_path);
    }
}
