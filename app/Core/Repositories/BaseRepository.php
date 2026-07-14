<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return $this->model->whereIn($this->model->getKeyName(), $ids)->delete();
    }

    public function bulkRestore(array $ids): int
    {
        if (method_exists($this->model, 'restore')) {
            return $this->model->withTrashed()->whereIn($this->model->getKeyName(), $ids)->restore();
        }
        return 0;
    }

    public function bulkUpdate(array $ids, array $data): int
    {
        return $this->model->whereIn($this->model->getKeyName(), $ids)->update($data);
    }

    public function bulkStatus(array $ids, string $status): int
    {
        return $this->bulkUpdate($ids, ['status' => $status]);
    }
}
