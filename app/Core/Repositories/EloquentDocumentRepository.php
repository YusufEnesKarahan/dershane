<?php

namespace App\Core\Repositories;

use App\Models\Document;
use App\DTOs\Document\DocumentSearchDTO;
use Illuminate\Support\Facades\DB;

class EloquentDocumentRepository implements DocumentRepository
{
    public function all()
    {
        return Document::with(['category', 'uploader'])->latest()->get();
    }

    public function find(int $id)
    {
        return Document::with(['category', 'uploader', 'versions.uploader', 'permissions.role', 'logs.user', 'documentable'])->findOrFail($id);
    }

    public function search(DocumentSearchDTO $dto)
    {
        $query = Document::with(['category', 'uploader']);

        if ($dto->query) {
            $query->where(function ($q) use ($dto) {
                $q->where('title', 'like', "%{$dto->query}%")
                  ->orWhere('file_name', 'like', "%{$dto->query}%")
                  ->orWhere('description', 'like', "%{$dto->query}%");
            });
        }

        if ($dto->category_id) {
            $query->where('category_id', $dto->category_id);
        }

        if ($dto->file_type) {
            $query->where('file_type', 'like', "%{$dto->file_type}%");
        }

        if ($dto->status) {
            $query->where('status', $dto->status);
        }

        if ($dto->date_from) {
            $query->whereDate('created_at', '>=', $dto->date_from);
        }

        if ($dto->date_to) {
            $query->whereDate('created_at', '<=', $dto->date_to);
        }

        return $query->latest()->get();
    }

    public function create(array $data)
    {
        return Document::create($data);
    }

    public function update(int $id, array $data)
    {
        $document = Document::findOrFail($id);
        $document->update($data);
        return $document;
    }

    public function delete(int $id)
    {
        $document = Document::findOrFail($id);
        return $document->delete();
    }

    public function restore(int $id)
    {
        $document = Document::withTrashed()->findOrFail($id);
        return $document->restore();
    }

    public function getAnalytics()
    {
        $totalDocuments = Document::count();
        $totalStorageBytes = Document::sum('file_size');
        
        $categoryDistribution = DB::table('documents')
            ->join('document_categories', 'documents.category_id', '=', 'document_categories.id')
            ->select('document_categories.name', 'document_categories.color', DB::raw('count(documents.id) as count'), DB::raw('sum(documents.file_size) as total_size'))
            ->whereNull('documents.deleted_at')
            ->groupBy('document_categories.id', 'document_categories.name', 'document_categories.color')
            ->get();

        $monthlyUploads = DB::table('documents')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(id) as count'), DB::raw('sum(file_size) as total_size'))
            ->whereNull('deleted_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return [
            'total_documents' => $totalDocuments,
            'total_storage_bytes' => $totalStorageBytes,
            'category_distribution' => $categoryDistribution,
            'monthly_uploads' => $monthlyUploads,
        ];
    }
}
