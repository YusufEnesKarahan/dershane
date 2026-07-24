<?php

namespace App\Core\Repositories;

use App\Models\DocumentCategory;

interface DocumentCategoryRepository
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
