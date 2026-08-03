<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Role;
use App\Domain\Notification\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AnnouncementService $announcementService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Announcement::class);

        $query = Announcement::where('branch_id', auth()->user()->branch_id)
            ->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query->paginate(15);
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();

        return view('admin.announcements.index', compact('announcements', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:announcement,system,absence,payment',
            'target_role' => 'nullable|string|exists:roles,name',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['created_by'] = auth()->id();

        $announcement = $this->announcementService->create($validated);

        if ($request->has('publish')) {
            $this->announcementService->publish($announcement);
            return redirect()->route('admin.announcements.index')->with('success', 'Duyuru başarıyla oluşturuldu ve yayınlandı.');
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Duyuru taslak olarak kaydedildi.');
    }

    public function publish(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        
        $this->announcementService->publish($announcement);
        
        return back()->with('success', 'Duyuru başarıyla yayınlandı.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        
        $announcement->delete();
        
        return back()->with('success', 'Duyuru başarıyla silindi.');
    }
}
