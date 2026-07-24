<?php

namespace App\DTOs\Inventory;

class CreateSupplierDTO
{
    public function __construct(
        public string $name,
        public ?string $phone,
        public ?string $email,
        public ?string $address,
        public ?string $taxNumber
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->input('name'),
            phone: $request->input('phone'),
            email: $request->input('email'),
            address: $request->input('address'),
            taxNumber: $request->input('tax_number')
        );
    }
}
