<?php

namespace App\DTOs\CRM;

class AssignLeadDTO
{
    public function __construct(
        public int $leadId,
        public ?int $toUserId = null,
        public ?int $branchId = null,
        public ?int $fromUserId = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['lead_id'],
            isset($data['to_user_id']) ? (int) $data['to_user_id'] : null,
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            isset($data['from_user_id']) ? (int) $data['from_user_id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'lead_id' => $this->leadId,
            'to_user_id' => $this->toUserId,
            'branch_id' => $this->branchId,
            'from_user_id' => $this->fromUserId,
        ];
    }
}
