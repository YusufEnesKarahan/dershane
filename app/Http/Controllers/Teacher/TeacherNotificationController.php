<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Student;
use App\Domain\Notification\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherNotificationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->user(), 15);

        return view('teacher.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            abort(403, 'Öğretmen profili bulunamadı.');
        }

        $students = Student::with('user')->get();

        return view('teacher.notifications.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|in:homework,attendance,exam,guidance,announcement,system',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if (!$student->user) {
            return back()->with('error', 'Öğrencinin kullanıcı hesabı bulunmuyor.');
        }

        $this->notificationService->sendToStudent(
            $student,
            $validated['title'],
            $validated['message'],
            $validated['type'] ?? 'system'
        );

        return redirect()->route('teacher.notifications.index')->with('success', 'Bildirim öğrenciye gönderildi.');
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('view', $notification);

        $this->notificationService->markAsRead($notification->id, auth()->user());

        return back()->with('success', 'Bildirim okundu olarak işaretlendi.');
    }
}
