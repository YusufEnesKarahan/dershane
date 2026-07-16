<?php

namespace App\Enums;

enum MediaVisibility: string
{
    case Public = 'public';
    case Private = 'private';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Herkese Açık',
            self::Private => 'Gizli',
        };
    }
}
