<?php

namespace App\Domain\System\Commands;

interface RemoteCommandHandlerInterface
{
    /**
     * Handle the remote command from HQ.
     *
     * @param array $payload
     * @return array The result array containing 'success' boolean and other data.
     */
    public function handle(array $payload): array;
}
