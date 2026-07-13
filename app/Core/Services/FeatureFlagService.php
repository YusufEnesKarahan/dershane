<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class FeatureFlagService
{
    protected EditionManager $editionManager;

    public function __construct(EditionManager $editionManager)
    {
        $this->editionManager = $editionManager;
    }

    /**
     * Determine if the given feature flag is active.
     */
    public function isActive(string $feature): bool
    {
        return $this->editionManager->has($feature);
    }

    /**
     * Get the name of the currently active SaaS edition.
     */
    public function getEditionName(): string
    {
        $edition = $this->editionManager->current();

        return Config::get("features.packages.{$edition}.name", 'Basic Edition');
    }
}
