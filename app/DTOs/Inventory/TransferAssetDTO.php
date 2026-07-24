<?php

namespace App\DTOs\Inventory;

class TransferAssetDTO
{
    public function __construct(
        public int $assetId,
        public ?int $fromLocationId,
        public int $toLocationId,
        public string $transferDate,
        public ?string $notes
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            assetId: (int) $request->input('asset_id'),
            fromLocationId: $request->input('from_location_id') ? (int) $request->input('from_location_id') : null,
            toLocationId: (int) $request->input('to_location_id'),
            transferDate: $request->input('transfer_date', now()->toDateString()),
            notes: $request->input('notes')
        );
    }
}
