<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementGroup;
use App\DTOs\Communication\SendAnnouncementDTO;
use App\Domain\Communication\Actions\SendAnnouncement;
use App\Core\Repositories\Interfaces\AnnouncementRepositoryInterface;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function __construct(protected AnnouncementRepositoryInterface $repository) {}

    public function index(Request $request)
    {
        $announcements = $this->repository->paginate(15, $request->all());
        $groups = AnnouncementGroup::all();

        return view('admin.announcements.index', compact('announcements', 'groups'));
    }

    public function store(Request $request, SendAnnouncement $action)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $dto = new SendAnnouncementDTO(
            $request->title,
            $request->content,
            $request->announcement_group_id ? (int) $request->announcement_group_id : null
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Duyuru başarıyla yayınlandı.');
    }
}
