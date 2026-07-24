<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\DTOs\Notification\CreateNotificationDTO;
use App\Domain\Notification\Actions\CreateNotification;
use App\Domain\Notification\Actions\MarkNotificationRead;
use App\Domain\System\Services\QueueService;
use App\Domain\Notification\Actions\UpdatePreference;
use App\DTOs\Notification\NotificationPreferenceDTO;
use App\Domain\Notification\Services\NotificationAnalyticsService;
use App\Core\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepositoryInterface $repository,
        protected NotificationAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Notification::class);

        $notifications = $this->repository->paginate(15, $request->all());
        $users = User::query()->select(['id', 'name', 'email'])->orderBy('name')->get();

        return view('admin.notifications.index', compact('notifications', 'users'));
    }

    public function dashboard()
    {
        $this->authorize('viewAny', Notification::class);
        return view('admin.notifications.dashboard', ['summary' => $this->analyticsService->summary()]);
    }

    public function store(Request $request, CreateNotification $create, QueueService $queue)
    {
        $this->authorize('create', Notification::class);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string',
            'channel' => 'required|in:panel,email,sms',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        $dto = new CreateNotificationDTO((int) $request->user_id, $request->title, $request->message, $request->type, $request->channel, $request->input('priority', 'normal'));

        $notification = $create->execute($dto);
        $queue->sendNotification($notification->id);

        return redirect()->back()->with('success', 'Bildirim başarıyla gönderildi ve iletim logu oluşturuldu.');
    }

    public function templates()
    {
        return view('admin.notifications.templates', ['templates' => \App\Models\NotificationTemplate::query()->latest()->get()]);
    }

    public function analytics()
    {
        $summary = $this->analyticsService->summary();
        $recentLogs = \App\Models\NotificationLog::query()->with('notification.user')->latest()->take(15)->get();

        return view('admin.notifications.analytics', compact('summary', 'recentLogs'));
    }

    public function markRead(Notification $notification, MarkNotificationRead $action)
    {
        $this->authorize('view', $notification);
        $action->execute($notification);
        return back()->with('success', 'Bildirim okundu olarak işaretlendi.');
    }

    public function preferences(Request $request)
    {
        $preference = \App\Models\NotificationPreference::firstOrCreate(['user_id' => $request->user()->id]);
        return view('admin.notifications.preferences', compact('preference'));
    }

    public function updatePreferences(Request $request, UpdatePreference $action)
    {
        $data = $request->validate(['email_enabled' => 'nullable|boolean', 'panel_enabled' => 'nullable|boolean', 'sms_enabled' => 'nullable|boolean']);
        $action->execute(new NotificationPreferenceDTO($request->user()->id, $request->boolean('email_enabled'), $request->boolean('panel_enabled'), $request->boolean('sms_enabled')));
        return back()->with('success', 'Bildirim tercihleri güncellendi.');
    }
}
