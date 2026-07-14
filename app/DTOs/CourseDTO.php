<?php
namespace App\DTOs;

class CourseDTO
{
    public function __construct(public array $data) {}

    public static function fromRequest($request): self
    {
        return new self($request->validated());
    }
}
