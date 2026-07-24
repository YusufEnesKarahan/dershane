<?php

namespace App\Core\Repositories;

use App\Models\DocumentCategory;

class EloquentDocumentCategoryRepository implements DocumentCategoryRepository
{
    public function all()
    {
        return DocumentCategory::withCount('documents')->get();
    }

    public function find(int $id)
    {
        return DocumentCategory::findOrFail($id);
    }

    public function create(array $data)
    {
        return DocumentCategory::create($data);
    }

    public function update(int $id, array $data)
    {
        $category = DocumentCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id)
    {
        $category = DocumentCategory::findOrFail($id);
        return $category->delete();
    }
}
