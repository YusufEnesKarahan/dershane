<?php
namespace App\Domain\Platform\Actions;

use App\Domain\Platform\Services\SettingsImporter;

class ImportSettingsAction
{
    public function __construct(protected SettingsImporter $importer) {}

    public function execute(string $data, bool $isEncrypted = false): bool
    {
        return $this->importer->importJson($data, $isEncrypted);
    }
}
