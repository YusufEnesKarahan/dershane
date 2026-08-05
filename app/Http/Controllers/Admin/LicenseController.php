<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Domain\License\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $licenses = License::with(['planModel', 'subscription.branch'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Fetch usage stats per branch
        $branches = Branch::all()->map(function ($branch) {
            $sCount = Student::where('branch_id', $branch->id)->count();
            $tCount = Teacher::where('branch_id', $branch->id)->count();
            $cCount = Classroom::where('branch_id', $branch->id)->count();

            $branch->student_usage = "{$sCount} / 200";
            $branch->teacher_usage = "{$tCount} / 10";
            $branch->classroom_usage = "{$cCount} / 5";

            return $branch;
        });

        return view('admin.licenses.index', compact('licenses', 'branches'));
    }

    public function activate(License $license)
    {
        $this->authorize('update', \App\Models\User::class);

        $this->licenseService->activateLicense($license);

        return back()->with('success', 'Lisans başarıyla aktifleştirildi.');
    }

    public function renew(Request $request, License $license)
    {
        $this->authorize('update', \App\Models\User::class);

        $days = (int) $request->input('days', 365);
        $this->licenseService->renewLicense($license, $days);

        return back()->with('success', "Lisans süresi {$days} gün uzatıldı.");
    }

    public function suspend(License $license)
    {
        $this->authorize('update', \App\Models\User::class);

        $this->licenseService->suspendLicense($license);

        return back()->with('warning', 'Lisans askıya alındı.');
    }

    public function cancel(License $license)
    {
        $this->authorize('update', \App\Models\User::class);

        $this->licenseService->cancelLicense($license);

        return back()->with('danger', 'Lisans iptal edildi.');
    }
}
