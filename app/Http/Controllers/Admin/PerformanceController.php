<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\PerformanceService;
use App\Domain\HR\Actions\EvaluateEmployee;
use App\DTOs\HR\PerformanceReviewDTO;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function __construct(
        protected PerformanceService $performanceService,
        protected EvaluateEmployee $evaluateAction
    ) {}

    public function index()
    {
        $reviews = $this->performanceService->allReviews();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.hr.performance', compact('reviews', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period' => 'required|string',
            'score' => 'required|integer|between:1,100',
        ]);

        $dto = PerformanceReviewDTO::fromRequest($request, Auth::id());
        $this->evaluateAction->execute($dto);

        return redirect()->route('admin.performance.index')->with('success', 'Performans değerlendirmesi kaydedildi.');
    }
}
