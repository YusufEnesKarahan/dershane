<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Domain\Notification\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Notification::class);

        $notifications = $this->notificationService->getUserNotifications(auth()->user(), 15);
        $allNotifications = Notification::with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $users = User::query()->select(['id', 'name', 'email'])->orderBy('name')->get();

        return view('admin.notifications.index', compact('notifications', 'allNotifications', 'users'));
    }

    public function create()
    {
        $this->authorize('create', Notification::class);

        $users = User::query()->select(['id', 'name', 'email'])->orderBy('name')->get();

        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Notification::class);

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:homework,attendance,exam,guidance,announcement,system',
            'receiver_type' => 'nullable|string|in:student,teacher,parent,admin',
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);
        $receiverType = $validated['receiver_type'] ?? 'admin';

        $this->notificationService->send(
            $receiver,
            $validated['title'],
            $validated['message'],
            $validated['type'],
            auth()->id(),
            $receiverType
        );

        return redirect()->route('admin.notifications.index')->with('success', 'Bildirim başarıyla oluşturuldu ve gönderildi.');
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
