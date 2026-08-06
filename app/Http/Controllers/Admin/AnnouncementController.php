<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementCategory;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Domain\Notification\Services\AnnouncementCmsService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected AnnouncementCmsService $cmsService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Announcement::class);

        $query = Announcement::with(['category', 'creator', 'branches', 'attachments'])
            ->pinned();

        // Branch filter
        $activeBranchId = session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;
        $query->where(function ($q) use ($activeBranchId) {
            $q->where('is_all_branches', true)
              ->orWhere('branch_id', $activeBranchId)
              ->orWhereHas('branches', function ($bq) use ($activeBranchId) {
                  $bq->where('branches.id', $activeBranchId);
              });
        });

        // Search filter (Title, Summary, Content, Category Name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Author Filter
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $announcements = $query->paginate(15)->withQueryString();
        $categories = AnnouncementCategory::all();
        $branches = Branch::all();
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Super Admin', 'Admin', 'Tenant Admin', 'staff']);
        })->get();

        $widgetData = $this->cmsService->getDashboardWidgetData();

        return view('admin.announcements.index', compact('announcements', 'categories', 'branches', 'authors', 'widgetData'));
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        $categories = AnnouncementCategory::all();
        $branches = Branch::all();
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();

        return view('admin.announcements.create', compact('categories', 'branches', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:announcement_categories,id',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'cover_image' => 'nullable|string|max:500',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date|after_or_equal:publish_at',
            'is_pinned' => 'nullable',
            'is_popup' => 'nullable',
            'is_all_branches' => 'nullable',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'notify_roles' => 'nullable|array',
            'send_notification' => 'nullable',
            'target_role' => 'nullable|string',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
        ]);

        $uploadedFiles = $request->file('attachments', []);

        $announcement = $this->cmsService->createAnnouncement($validated, $uploadedFiles);

        return redirect()->route('admin.announcements.index')->with('success', 'Duyuru başarıyla kaydedildi.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->load(['category', 'branches', 'attachments']);
        $categories = AnnouncementCategory::all();
        $branches = Branch::all();
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();

        return view('admin.announcements.edit', compact('announcement', 'categories', 'branches', 'roles'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:announcement_categories,id',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'cover_image' => 'nullable|string|max:500',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date',
            'is_pinned' => 'nullable',
            'is_popup' => 'nullable',
            'is_all_branches' => 'nullable',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'notify_roles' => 'nullable|array',
            'target_role' => 'nullable|string',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
        ]);

        $uploadedFiles = $request->file('attachments', []);

        $this->cmsService->updateAnnouncement($announcement, $validated, $uploadedFiles);

        return redirect()->route('admin.announcements.index')->with('success', 'Duyuru bilgileri güncellendi.');
    }

    public function publish(Announcement $announcement, Request $request)
    {
        $this->authorize('update', $announcement);
        
        $this->cmsService->publish($announcement, true);
        
        return back()->with('success', 'Duyuru başarıyla yayınlandı.');
    }

    public function archive(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        
        $this->cmsService->archive($announcement);
        
        return back()->with('success', 'Duyuru arşive kaldırıldı.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        
        $announcement->delete();
        
        return back()->with('success', 'Duyuru silindi.');
    }

    public function markPopupSeen(Announcement $announcement)
    {
        session(['popup_announcement_seen_' . $announcement->id => true]);
        return response()->json(['success' => true]);
    }
}
