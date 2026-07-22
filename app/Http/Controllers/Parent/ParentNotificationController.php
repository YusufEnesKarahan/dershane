<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Domain\Parent\Services\ParentNotificationService;
use App\Models\ParentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentNotificationController extends Controller
{
    public function __construct(protected ParentNotificationService $service) {}

    public function index()
    {
        $parent = Auth::user();
        if (!$parent) {
            return redirect()->route('login');
        }

        // Mark unread as read automatically upon listing
        ParentNotification::where('parent_id', $parent->id)->update(['is_read' => true]);

        $notifications = $this->service->getNotifications($parent->id);

        return view('parent.notifications', compact('notifications'));
    }
}
