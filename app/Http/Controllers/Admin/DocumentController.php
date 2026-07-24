<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Document\Services\DocumentService;
use App\Domain\Document\Services\DocumentCategoryService;
use App\Domain\Document\Services\DocumentVersionService;
use App\Domain\Document\Services\DocumentPermissionService;
use App\Domain\Document\Services\DocumentAnalyticsService;
use App\Domain\Document\Actions\CreateDocument;
use App\Domain\Document\Actions\UpdateDocument;
use App\Domain\Document\Actions\UploadDocumentVersion;
use App\Domain\Document\Actions\DeleteDocument;
use App\Domain\Document\Actions\RestoreDocument;
use App\Domain\Document\Actions\ShareDocument;
use App\DTOs\Document\CreateDocumentDTO;
use App\DTOs\Document\UpdateDocumentDTO;
use App\DTOs\Document\UploadVersionDTO;
use App\DTOs\Document\DocumentPermissionDTO;
use App\Models\DocumentCategory;
use App\Models\Role;
use App\Models\DocumentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documentService,
        protected DocumentVersionService $versionService,
        protected DocumentPermissionService $permissionService,
        protected DocumentAnalyticsService $analyticsService,
        protected CreateDocument $createAction,
        protected UpdateDocument $updateAction,
        protected UploadDocumentVersion $versionAction,
        protected DeleteDocument $deleteAction,
        protected RestoreDocument $restoreAction,
        protected ShareDocument $shareAction
    ) {}

    public function dashboard()
    {
        $metrics = $this->analyticsService->getDashboardMetrics();
        return view('admin.documents.dashboard', compact('metrics'));
    }

    public function index()
    {
        $documents = $this->documentService->allDocuments();
        $categories = DocumentCategory::where('is_active', true)->get();
        return view('admin.documents.index', compact('documents', 'categories'));
    }

    public function create()
    {
        $categories = DocumentCategory::where('is_active', true)->get();
        return view('admin.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:document_categories,id',
            'file' => 'nullable|file|max:51200',
        ]);

        $dto = CreateDocumentDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('admin.documents.index')->with('success', 'Belge başarıyla yüklendi.');
    }

    public function show(int $id)
    {
        $document = $this->documentService->findDocument($id);
        $versions = $this->versionService->getVersionHistory($id);
        $permissions = $this->permissionService->getPermissionsForDocument($id);
        $roles = Role::all();
        return view('admin.documents.show', compact('document', 'versions', 'permissions', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:document_categories,id',
        ]);

        $dto = UpdateDocumentDTO::fromRequest($request);
        $this->updateAction->execute($id, $dto);

        return redirect()->route('admin.documents.show', $id)->with('success', 'Belge bilgileri güncellendi.');
    }

    public function destroy(int $id)
    {
        $this->deleteAction->execute($id);
        return redirect()->route('admin.documents.index')->with('success', 'Belge silindi.');
    }

    public function restore(int $id)
    {
        $this->restoreAction->execute($id);
        return redirect()->route('admin.documents.index')->with('success', 'Belge geri yüklendi.');
    }

    public function uploadVersion(Request $request, int $id)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
        ]);

        $dto = UploadVersionDTO::fromRequest($request, $id);
        $this->versionAction->execute($dto);

        return redirect()->route('admin.documents.show', $id)->with('success', 'Yeni versiyon yüklendi.');
    }

    public function share(Request $request, int $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $dto = DocumentPermissionDTO::fromRequest($request, $id);
        $this->shareAction->execute($dto);

        return redirect()->route('admin.documents.show', $id)->with('success', 'Erişim yetkisi tanımlandı.');
    }

    public function download(int $id)
    {
        $document = $this->documentService->findDocument($id);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'download',
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $relative = str_replace('storage/', '', $document->file_path);
        if (Storage::disk('public')->exists($relative)) {
            return Storage::disk('public')->download($relative, $document->file_name);
        }

        return redirect()->back()->with('error', 'Fiziksel dosya bulunamadı.');
    }
}
