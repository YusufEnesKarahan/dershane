<?php

namespace App\DTOs\CRM;

class LeadNoteDTO
{
    public function __construct(
        public int $leadId,
        public string $noteText,
        public ?int $userId = null,
        public ?string $filePath = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['lead_id'],
            $data['note_text'],
            isset($data['user_id']) ? (int) $data['user_id'] : null,
            $data['file_path'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'lead_id' => $this->leadId,
            'note_text' => $this->noteText,
            'user_id' => $this->userId,
            'file_path' => $this->filePath,
        ];
    }
}
