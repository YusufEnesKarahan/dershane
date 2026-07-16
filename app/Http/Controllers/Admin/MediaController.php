<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use App\DTOs\Media\UploadMediaDTO;
use App\DTOs\Media\MediaFilterDTO;
use App\Http\Requests\Admin\Media\UploadMediaRequest;
use App\Domain\Media\Actions\UploadMediaAction;
use App\Domain\Media\Actions\DeleteMediaAction;
use App\Domain\Media\Services\MediaUsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function __construct(
        protected MediaUsageService $usageService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Media::class);

        $filters = MediaFilterDTO::fromRequest($request->all());
        
        $folderId = $filters->folder_id;
        $currentFolder = $folderId ? MediaFolder::find($folderId) : null;
        
        $folders = MediaFolder::where('parent_id', $folderId)->orderBy('order_column')->get();
        $folderTree = MediaFolder::whereNull('parent_id')->with('children')->orderBy('order_column')->get();

        // Query media
        $mediaQuery = Media::query();
        if ($folderId) {
            $mediaQuery->where('folder_id', $folderId);
        } else {
            $mediaQuery->whereNull('folder_id');
        }

        if ($filters->search) {
            $search = $filters->search;
            $mediaQuery->where(function($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('alt', 'like', "%{$search}%");
            });
        }

        if ($filters->type) {
            if ($filters->type === 'image') {
                $mediaQuery->where('mime_type', 'like', 'image/%');
            } elseif ($filters->type === 'video') {
                $mediaQuery->where('mime_type', 'like', 'video/%');
            } elseif ($filters->type === 'audio') {
                $mediaQuery->where('mime_type', 'like', 'audio/%');
            } else {
                $mediaQuery->where('mime_type', 'not like', 'image/%')
                           ->where('mime_type', 'not like', 'video/%')
                           ->where('mime_type', 'not like', 'audio/%');
            }
        }

        $mediaList = $mediaQuery->orderBy('created_at', 'desc')->paginate(24);
        $collections = config('media.collections', []);

        return view('admin.media.index', compact('mediaList', 'folders', 'folderTree', 'currentFolder', 'collections'));
    }

    public function store(UploadMediaRequest $request, UploadMediaAction $action)
    {
        $dto = UploadMediaDTO::fromRequest($request->validated());
        $media = $action->execute($dto);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'uuid' => $media->uuid,
                    'url' => $media->getUrl(),
                    'title' => $media->title,
                    'original_name' => $media->original_name,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

    public function destroy(Media $media, DeleteMediaAction $action)
    {
        $this->authorize('delete', $media);

        // Usage check
        $usages = $this->usageService->getUsages($media->id);
        if ($usages->isNotEmpty()) {
            return redirect()->back()->with('error', 'Bu dosya başka bir modülde kullanıldığı için silinemez.');
        }

        $action->execute($media);

        return redirect()->back()->with('success', 'File deleted successfully.');
    }

    public function usages(Media $media)
    {
        $usages = $this->usageService->getUsages($media->id);
        return response()->json([
            'count' => $usages->count(),
            'usages' => $usages->map(fn($u) => [
                'model' => class_basename($u->model_type),
                'id' => $u->model_id,
                'field' => $u->field,
            ])
        ]);
    }

    // Ajax route for interactive media picker
    public function pickerList(Request $request)
    {
        $filters = MediaFilterDTO::fromRequest($request->all());
        
        $query = Media::query();
        if ($filters->folder_id) {
            $query->where('folder_id', $filters->folder_id);
        } else {
            $query->whereNull('folder_id');
        }

        if ($filters->search) {
            $query->where('original_name', 'like', "%{$filters->search}%");
        }

        $media = $query->orderBy('created_at', 'desc')->get();

        return response()->json($media->map(fn($m) => [
            'id' => $m->id,
            'uuid' => $m->uuid,
            'title' => $m->title,
            'url' => $m->getUrl(),
            'mime_type' => $m->mime_type,
            'thumb' => $m->getUrl('thumb')
        ]));
    }

    public function download(Media $media, Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Invalid or expired download signature.');
        }

        $dir = $media->directory ? $media->directory . '/' : '';
        return \Illuminate\Support\Facades\Storage::disk($media->disk)->download($dir . $media->filename, $media->original_name);
    }
}
