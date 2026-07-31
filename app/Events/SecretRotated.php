<?php

namespace App\Events;

use App\Models\HQSecretVault;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SecretRotated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $secret;

    public function __construct(HQSecretVault $secret)
    {
        $this->secret = $secret;
    }
}
