<?php

namespace App\Core\Repositories\Interfaces;

interface AssetRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function allCategories();
    public function allLocations();
    public function allAssignments();
    public function findAssignment(int $id);
    public function createAssignment(array $data);
    public function updateAssignment(int $id, array $data);
}
