<?php
namespace App\DTOs\System;
class AutomationLogDTO { public function __construct(public readonly string $jobName, public readonly string $status = 'running', public readonly array $payload = [], public readonly ?\DateTimeInterface $startedAt = null, public readonly ?\DateTimeInterface $completedAt = null, public readonly ?string $errorMessage = null) {} }
