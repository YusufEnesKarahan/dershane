<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Domain\Notification\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentNotificationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->user(), 15);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('view', $notification);

        $this->notificationService->markAsRead($notification->id, auth()->user());

        return back()->with('success', 'Bildirim okundu olarak işaretlendi.');
    }

    public function markAllRead()
    {
        $this->notificationService->markAllAsRead(auth()->user());

        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }
}
