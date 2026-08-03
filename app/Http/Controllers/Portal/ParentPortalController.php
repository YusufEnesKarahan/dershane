<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Domain\Portal\Services\ParentPortalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    public function __construct(protected ParentPortalService $service) {}

    public function dashboard(Request $request)
    {
        $guardian = $this->service->getGuardianByUserId(Auth::id());
        
        if (!$guardian) {
            abort(403, 'Veli profili bulunamadı.');
        }

        $children = $this->service->getChildren($guardian->id);
        
        $childrenData = [];
        foreach ($children as $child) {
            $childrenData[] = [
                'student' => $child,
                'attendance_stats' => $this->service->getChildAttendanceStats($child->id),
                'recent_attendance' => $this->service->getChildAttendance($child->id, 5),
            ];
        }

        return view('portal.parent.dashboard', compact('guardian', 'childrenData'));
    }
}
