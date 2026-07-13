<?php

declare(strict_types=1);

use App\Core\Services\EditionManager;
use App\Core\Services\FeatureFlagService;

if (! function_exists('edition')) {
    /**
     * Get the EditionManager instance or current active edition slug.
     */
    function edition(): EditionManager
    {
        return app(EditionManager::class);
    }
}

if (! function_exists('edition_name')) {
    /**
     * Get display name of currently active SaaS edition.
     */
    function edition_name(): string
    {
        return app(FeatureFlagService::class)->getEditionName();
    }
}

if (! function_exists('edition_color')) {
    /**
     * Get color theme configuration for current active SaaS edition.
     */
    function edition_color(): string
    {
        $edition = edition()->current();

        return match ($edition) {
            'basic' => 'neutral',
            'professional' => 'primary',
            'ultimate' => 'secondary',
            default => 'neutral',
        };
    }
}

if (! function_exists('edition_badge')) {
    /**
     * Render HTML badge representing currently active SaaS edition.
     */
    function edition_badge(): string
    {
        $name = edition_name();
        $color = edition_color();

        $bgClass = match ($color) {
            'primary' => 'bg-primary/10 text-primary border-primary/20',
            'secondary' => 'bg-secondary/10 text-secondary border-secondary/20',
            default => 'bg-neutral-100 text-neutral-800 border-neutral-200',
        };

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border '.$bgClass.'">'.e($name).'</span>';
    }
}
