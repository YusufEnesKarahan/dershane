<?php
namespace App\DTOs\Platform;

class BrandDTO
{
    public function __construct(
        public string $company_name,
        public string $short_name,
        public string $legal_name,
        public ?string $logo_url = null,
        public ?string $dark_logo_url = null,
        public ?string $favicon_url = null
    ) {}
}
