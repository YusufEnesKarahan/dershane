<?php
namespace App\Domain\Media\Actions;

use App\DTOs\Media\UploadMediaDTO;
use App\Domain\Media\Services\MediaService;
use App\Models\Media;

class UploadMediaAction
{
    public function __construct(protected MediaService $service) {}

    public function execute(UploadMediaDTO $dto): Media
    {
        return $this->service->upload($dto);
    }
}
