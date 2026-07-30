<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HQWorkflowServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::subscribe(\App\Listeners\HQWorkflowEventSubscriber::class);
    }
}
