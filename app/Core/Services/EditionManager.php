<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Enums\EditionType;
use Illuminate\Support\Facades\Config;

class EditionManager
{
    /**
     * Get the current active Edition Type enum.
     */
    public function edition(): EditionType
    {
        $slug = Config::get('features.edition', 'basic');

        return EditionType::tryFrom($slug) ?? EditionType::BASIC;
    }

    /**
     * Get the active edition's string slug.
     */
    public function current(): string
    {
        return $this->edition()->value;
    }

    /**
     * Determine if current edition is Basic.
     */
    public function isBasic(): bool
    {
        return $this->edition() === EditionType::BASIC;
    }

    /**
     * Determine if current edition is Professional.
     */
    public function isProfessional(): bool
    {
        return $this->edition() === EditionType::PROFESSIONAL;
    }

    /**
     * Determine if current edition is Ultimate.
     */
    public function isUltimate(): bool
    {
        return $this->edition() === EditionType::ULTIMATE;
    }

    /**
     * Determine if the active edition has access to a specific feature.
     */
    public function has(string $feature): bool
    {
        $currentEdition = $this->current();
        $packages = Config::get('features.packages', []);

        // Direct check in the configured features package
        if (isset($packages[$currentEdition]['features'][$feature])) {
            return (bool) $packages[$currentEdition]['features'][$feature];
        }

        // Fallback to global override flags
        $globalFlags = Config::get('features.flags', []);
        if (isset($globalFlags[$feature])) {
            return (bool) $globalFlags[$feature];
        }

        return false;
    }
}
