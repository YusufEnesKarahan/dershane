<?php

namespace App\DTOs\CRM;

class CreateFollowupDTO
{
    public function __construct(
        public int $leadId,
        public string $followupDate,
        public ?string $reminderNote = null,
        public string $priority = 'Medium',
        public ?int $userId = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['lead_id'],
            $data['followup_date'],
            $data['reminder_note'] ?? null,
            $data['priority'] ?? 'Medium',
            isset($data['user_id']) ? (int) $data['user_id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'lead_id' => $this->leadId,
            'followup_date' => $this->followupDate,
            'reminder_note' => $this->reminderNote,
            'priority' => $this->priority,
            'user_id' => $this->userId,
        ];
    }
}
