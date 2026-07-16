<?php
namespace App\DTOs\Platform;

class ThemeDTO
{
    public function __construct(
        public string $primary_color,
        public string $secondary_color,
        public string $accent_color,
        public string $background_color,
        public string $sidebar_color,
        public string $border_radius,
        public string $spacing,
        public string $button_style,
        public string $card_style,
        public string $typography
    ) {}
}
