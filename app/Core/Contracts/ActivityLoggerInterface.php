<?php
namespace App\Core\Contracts;

interface ActivityLoggerInterface
{
    public function log(string $action, array $data = [], ?int $userId = null): void;
}
