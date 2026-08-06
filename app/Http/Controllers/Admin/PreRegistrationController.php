<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreRegistration;
use App\Models\Classroom;
use App\Models\Branch;
use App\Models\User;
use App\Http\Requests\StorePreRegistrationRequest;
use App\Http\Requests\ConvertPreRegistrationRequest;
use App\Domain\Finance\Services\PreRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PreRegistrationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PreRegistrationService $preRegService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', PreRegistration::class);

        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;

        $query = PreRegistration::with(['assignedUser', 'branch', 'convertedStudent'])
            ->where('branch_id', $branchId)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $preRegistrations = $query->paginate(15)->withQueryString();

        return view('admin.pre_registrations.index', compact('preRegistrations'));
    }

    public function create()
    {
        $this->authorize('create', PreRegistration::class);

        $staffUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Super Admin', 'Admin', 'Tenant Admin', 'Branch Admin', 'Secretary', 'staff']);
        })->get();

        return view('admin.pre_registrations.create', compact('staffUsers'));
    }

    public function store(StorePreRegistrationRequest $request)
    {
        $this->authorize('create', PreRegistration::class);

        $this->preRegService->createPreRegistration($request->validated());

        return redirect()->route('admin.pre-registrations.index')->with('success', 'Ön kayıt başarıyla oluşturuldu.');
    }

    public function edit(PreRegistration $preRegistration)
    {
        $this->authorize('update', $preRegistration);

        $staffUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Super Admin', 'Admin', 'Tenant Admin', 'Branch Admin', 'Secretary', 'staff']);
        })->get();

        return view('admin.pre_registrations.edit', compact('preRegistration', 'staffUsers'));
    }

    public function update(StorePreRegistrationRequest $request, PreRegistration $preRegistration)
    {
        $this->authorize('update', $preRegistration);

        $this->preRegService->updatePreRegistration($preRegistration, $request->validated());

        return redirect()->route('admin.pre-registrations.index')->with('success', 'Ön kayıt bilgileri güncellendi.');
    }

    public function destroy(PreRegistration $preRegistration)
    {
        $this->authorize('delete', $preRegistration);

        $preRegistration->delete();

        return back()->with('success', 'Ön kayıt silindi.');
    }

    public function showConvertForm(PreRegistration $preRegistration)
    {
        $this->authorize('convert', $preRegistration);

        $classrooms = Classroom::all();

        return view('admin.pre_registrations.convert', compact('preRegistration', 'classrooms'));
    }

    public function convertToStudent(ConvertPreRegistrationRequest $request, PreRegistration $preRegistration)
    {
        $this->authorize('convert', $preRegistration);

        $student = $this->preRegService->convertToStudent($preRegistration, $request->validated());

        return redirect()->route('admin.students.show', $student->id)->with('success', "Ön kayıt başarıyla kesin kayıda dönüştürüldü! Öğrenci No: {$student->student_number}");
    }
}
