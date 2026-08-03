<?php

namespace App\Http\Controllers;

use App\Domain\Notification\Services\NotificationService;
use App\Domain\Auth\Dictionaries\PermissionDictionary;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index()
    {
        abort_unless(auth()->user()->hasPermission(PermissionDictionary::NOTIFICATIONS_VIEW) || auth()->user()->hasPermission(PermissionDictionary::NOTIFICATIONS_READ), 403);
        
        $notifications = auth()->user()->notifications()->paginate(15);
        $unreadCount = auth()->user()->unreadNotifications()->count();
        
        return view('portal.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Request $request, $id = null)
    {
        abort_unless(auth()->user()->hasPermission(PermissionDictionary::NOTIFICATIONS_READ), 403);
        
        $this->notificationService->markAsRead(auth()->user(), $id);
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Bildirim okundu olarak işaretlendi.');
    }
}
