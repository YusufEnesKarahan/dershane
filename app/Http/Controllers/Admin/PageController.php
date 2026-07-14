<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\DTOs\CMS\CreatePageDTO;
use App\DTOs\CMS\UpdatePageDTO;
use App\DTOs\CMS\PageFilterDTO;
use App\Http\Requests\Admin\CMS\StorePageRequest;
use App\Http\Requests\Admin\CMS\UpdatePageRequest;
use App\Domain\CMS\Services\PageService;
use App\Domain\CMS\Actions\CreatePageAction;
use App\Domain\CMS\Actions\UpdatePageAction;
use App\Domain\CMS\Actions\DeletePageAction;
use App\Domain\CMS\Actions\RestorePageAction;
use App\Domain\CMS\Actions\PublishPageAction;
use App\Domain\CMS\Actions\DuplicatePageAction;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(protected PageService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Page::class);

        $filters = PageFilterDTO::fromRequest($request->all());
        $pages = $this->service->paginate($filters);
        
        $tree = app(\App\Core\Repositories\Interfaces\PageRepositoryInterface::class)->getTree();

        return view('admin.pages.index', compact('pages', 'tree'));
    }

    public function create()
    {
        $this->authorize('create', Page::class);
        $templates = ['default' => 'Varsayılan', 'about' => 'Hakkımızda', 'faq' => 'SSS', 'contact' => 'İletişim'];
        $pages = Page::whereNull('parent_id')->get();

        return view('admin.pages.create', compact('templates', 'pages'));
    }

    public function store(StorePageRequest $request, CreatePageAction $action)
    {
        $dto = CreatePageDTO::fromRequest($request->validated());
        $action->execute($dto);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        $this->authorize('update', $page);
        $templates = ['default' => 'Varsayılan', 'about' => 'Hakkımızda', 'faq' => 'SSS', 'contact' => 'İletişim'];
        $pages = Page::whereNull('parent_id')->where('id', '!=', $page->id)->get();

        return view('admin.pages.edit', compact('page', 'templates', 'pages'));
    }

    public function update(UpdatePageRequest $request, Page $page, UpdatePageAction $action)
    {
        $dto = UpdatePageDTO::fromRequest($request->validated());
        $action->execute($page, $dto);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page, DeletePageAction $action)
    {
        $this->authorize('delete', $page);
        $action->execute($page);

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }

    public function restore(int $id, RestorePageAction $action)
    {
        $page = Page::withTrashed()->findOrFail($id);
        $this->authorize('update', $page);
        $action->execute($id);

        return redirect()->route('admin.pages.index')->with('success', 'Page restored successfully.');
    }

    public function publish(Page $page, Request $request, PublishPageAction $action)
    {
        $this->authorize('update', $page);
        $status = $request->input('status', 'published');
        $action->execute($page, $status);

        return redirect()->route('admin.pages.index')->with('success', "Page status updated to {$status}.");
    }

    public function duplicate(Page $page, DuplicatePageAction $action)
    {
        $this->authorize('create', Page::class);
        $action->execute($page);

        return redirect()->route('admin.pages.index')->with('success', 'Page duplicated successfully.');
    }

    public function preview(Page $page)
    {
        $this->authorize('view', $page);
        return view('admin.pages.preview', compact('page'));
    }

    public function bulk(Request $request, \App\Core\Repositories\Interfaces\PageRepositoryInterface $repository)
    {
        $this->authorize('delete', Page::class);

        $action = $request->input('bulk_action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No pages selected.');
        }

        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $page = Page::find($id);
                    if ($page && ($page->isSystemPage() || $page->isHomepage())) {
                        return redirect()->back()->with('error', 'System or Homepage pages cannot be deleted.');
                    }
                }
                $repository->bulkDelete($ids);
                break;
            case 'publish':
                Page::whereIn('id', $ids)->update(['status' => 'published', 'published_at' => now()]);
                break;
            case 'archive':
                Page::whereIn('id', $ids)->update(['status' => 'archived', 'published_at' => null]);
                break;
        }

        return redirect()->route('admin.pages.index')->with('success', 'Bulk action completed successfully.');
    }
}
