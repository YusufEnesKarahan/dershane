<?php

namespace App\Enums;

enum PageStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Taslak',
            self::Review => 'İncelemede',
            self::Published => 'Yayında',
            self::Archived => 'Arşivlendi',
        };
    }
}
