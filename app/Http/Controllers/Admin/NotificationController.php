<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\DTOs\Communication\CreateNotificationDTO;
use App\Domain\Communication\Actions\SendNotification;
use App\Domain\Communication\Services\NotificationAnalyticsService;
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
        $users = User::all();

        return view('admin.notifications.index', compact('notifications', 'users'));
    }

    public function store(Request $request, SendNotification $action)
    {
        $this->authorize('create', Notification::class);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string',
        ]);

        $dto = new CreateNotificationDTO(
            (int) $request->user_id,
            $request->title,
            $request->content,
            $request->type
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Bildirim başarıyla gönderildi ve iletim logu oluşturuldu.');
    }

    public function templates()
    {
        $templates = NotificationTemplate::all();
        return view('admin.notifications.templates', compact('templates'));
    }

    public function analytics()
    {
        $summary = $this->analyticsService->getSummary();
        $recentLogs = \App\Models\NotificationLog::orderBy('created_at', 'desc')->take(15)->get();

        return view('admin.notifications.analytics', compact('summary', 'recentLogs'));
    }
}
