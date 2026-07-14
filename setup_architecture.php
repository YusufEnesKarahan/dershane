<?php

function createClass($path, $content) {
    $dir = dirname(__DIR__ . '/' . $path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(__DIR__ . '/' . $path, $content);
}

// DTOs
$models = ['Page', 'Blog', 'Teacher', 'Course', 'Lead', 'ContactMessage'];
foreach ($models as $model) {
    createClass('app/DTOs/' . $model . 'DTO.php', "<?php
namespace App\DTOs;

class {$model}DTO
{
    public function __construct(public array \$data) {}

    public static function fromRequest(\$request): self
    {
        return new self(\$request->validated());
    }
}
");
}

// Repository Interfaces
createClass('app/Core/Repositories/Interfaces/BaseRepositoryInterface.php', "<?php
namespace App\Core\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all();
    public function find(\$id);
    public function create(array \$data);
    public function update(\$id, array \$data);
    public function delete(\$id);
}
");

foreach ($models as $model) {
    createClass('app/Core/Repositories/Interfaces/' . $model . 'RepositoryInterface.php', "<?php
namespace App\Core\Repositories\Interfaces;

interface {$model}RepositoryInterface extends BaseRepositoryInterface
{
}
");
}

// Base Repository
createClass('app/Core/Repositories/BaseRepository.php', "<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected \$model;

    public function __construct(Model \$model)
    {
        \$this->model = \$model;
    }

    public function all()
    {
        return \$this->model->all();
    }

    public function find(\$id)
    {
        return \$this->model->findOrFail(\$id);
    }

    public function create(array \$data)
    {
        return \$this->model->create(\$data);
    }

    public function update(\$id, array \$data)
    {
        \$record = \$this->find(\$id);
        \$record->update(\$data);
        return \$record;
    }

    public function delete(\$id)
    {
        return \$this->find(\$id)->delete();
    }
}
");

// Repository Implementations
foreach ($models as $model) {
    createClass('app/Core/Repositories/' . $model . 'Repository.php', "<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\\{$model}RepositoryInterface;
use App\Models\\{$model};

class {$model}Repository extends BaseRepository implements {$model}RepositoryInterface
{
    public function __construct({$model} \$model)
    {
        parent::__construct(\$model);
    }
}
");
}

// Services
foreach ($models as $model) {
    createClass('app/Core/Services/' . $model . 'Service.php', "<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\\{$model}RepositoryInterface;
use App\DTOs\\{$model}DTO;

class {$model}Service
{
    protected \$repository;

    public function __construct({$model}RepositoryInterface \$repository)
    {
        \$this->repository = \$repository;
    }

    public function getAll()
    {
        return \$this->repository->all();
    }

    public function create({$model}DTO \$dto)
    {
        return \$this->repository->create(\$dto->data);
    }

    public function update(\$id, {$model}DTO \$dto)
    {
        return \$this->repository->update(\$id, \$dto->data);
    }

    public function delete(\$id)
    {
        return \$this->repository->delete(\$id);
    }
}
");
}

echo "Architecture classes created successfully.\n";
