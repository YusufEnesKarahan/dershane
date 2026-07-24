<?php
namespace App\Http\Controllers\Admin;
use App\Domain\Notification\Services\NotificationAnalyticsService;
use App\Http\Controllers\Controller;
class NotificationAnalyticsController extends Controller { public function __invoke(NotificationAnalyticsService $analytics) { return view('admin.notifications.analytics', ['summary' => $analytics->summary(), 'recentLogs' => \App\Models\NotificationLog::query()->latest()->take(15)->get()]); } }
