<?php
namespace App\DTOs\Platform;

class SeoDefaultsDTO
{
    public function __construct(
        public string $meta_title,
        public string $meta_description,
        public ?string $meta_keywords = null,
        public string $robots = 'index, follow'
    ) {}
}
