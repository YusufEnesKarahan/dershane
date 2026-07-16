<?php
namespace App\Domain\Platform\Actions;

use App\Domain\Platform\Services\SettingsExporter;

class ExportSettingsAction
{
    public function __construct(protected SettingsExporter $exporter) {}

    public function execute(bool $encrypt = false): string
    {
        return $this->exporter->exportJson($encrypt);
    }
}
