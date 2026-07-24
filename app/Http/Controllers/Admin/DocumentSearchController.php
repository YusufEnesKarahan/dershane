<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Document\Services\DocumentService;
use App\Domain\Document\Services\DocumentAnalyticsService;
use App\DTOs\Document\DocumentSearchDTO;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentSearchController extends Controller
{
    public function __construct(
        protected DocumentService $documentService,
        protected DocumentAnalyticsService $analyticsService
    ) {}

    public function search(Request $request)
    {
        $dto = DocumentSearchDTO::fromRequest($request);
        $documents = $this->documentService->searchDocuments($dto);
        $categories = DocumentCategory::where('is_active', true)->get();

        return view('admin.documents.index', compact('documents', 'categories'));
    }

    public function analytics()
    {
        $metrics = $this->analyticsService->getDashboardMetrics();
        return view('admin.documents.analytics', compact('metrics'));
    }
}
