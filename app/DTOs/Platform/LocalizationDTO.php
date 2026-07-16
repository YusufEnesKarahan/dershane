<?php
namespace App\DTOs\Platform;

class LocalizationDTO
{
    public function __construct(
        public string $default_locale = 'tr',
        public string $timezone = 'Europe/Istanbul'
    ) {}
}
