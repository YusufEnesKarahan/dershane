<?php
namespace App\DTOs\Platform;

class SecuritySettingsDTO
{
    public function __construct(
        public bool $force_https = false,
        public bool $two_factor_auth = false,
        public int $session_lifetime = 120
    ) {}
}
