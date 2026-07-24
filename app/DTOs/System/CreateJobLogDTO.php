<?php
namespace App\DTOs\System;
class CreateJobLogDTO { public function __construct(public readonly string $jobName, public readonly string $status, public readonly array $payload = [], public readonly ?\DateTimeInterface $startedAt = null, public readonly ?\DateTimeInterface $completedAt = null, public readonly ?string $errorMessage = null) {} }
