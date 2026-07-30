<?php

namespace App\Events;

use App\Models\HQApiKey;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApiKeyRevoked
{
    use Dispatchable, SerializesModels;

    public $apiKey;

    public function __construct(HQApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
    }
}
