<?php
namespace App\DTOs\Platform;

class MailSettingsDTO
{
    public function __construct(
        public string $host,
        public int $port,
        public string $username,
        public string $password,
        public string $encryption = 'tls',
        public string $from_address,
        public string $from_name
    ) {}
}
