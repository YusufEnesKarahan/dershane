<?php

declare(strict_types=1);

use App\Core\Services\EditionManager;
use App\Core\Services\FeatureFlagService;
use App\Models\Branch;
use App\Models\Plan;

if (! function_exists('edition')) {
    /**
     * Get the EditionManager instance or current active edition slug.
     */
    function edition(): EditionManager
    {
        return app(EditionManager::class);
    }
}

if (! function_exists('subscription_plan')) {
    function subscription_plan(?Branch $branch = null): ?Plan
    {
        return \App\Support\SaaS::currentPlan($branch);
    }
}

if (! function_exists('subscription_can')) {
    function subscription_can(string $feature, ?Branch $branch = null): bool
    {
        return \App\Support\SaaS::can($feature, $branch);
    }
}

if (! function_exists('subscription_limit')) {
    function subscription_limit(string $key, ?Branch $branch = null): ?int
    {
        return \App\Support\SaaS::limit($key, $branch);
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
