<?php
namespace App\Domain\Media\Conversions;

use App\Models\Media;

interface ConversionStrategyInterface
{
    public function canConvert(string $mimeType): bool;
    public function convert(Media $media): array; // returns array of variants info
}
