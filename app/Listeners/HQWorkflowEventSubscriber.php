<?php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use App\Domain\HQ\Services\Workflow\WorkflowEngineService;

class HQWorkflowEventSubscriber
{
    protected WorkflowEngineService $engine;

    public function __construct(WorkflowEngineService $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Handle the events.
     */
    public function handleSystemEvent($event)
    {
        $eventName = get_class($event);
        
        // Extract payload and tenant context if they exist
        $payload = [];
        $tenant = null;
        
        // Conventions: $event->tenant or $event->getTenant(), $event->payload or $event->toArray()
        if (property_exists($event, 'tenant')) {
            $tenant = $event->tenant;
        } elseif (method_exists($event, 'getTenant')) {
            $tenant = $event->getTenant();
        }
        
        if (property_exists($event, 'payload')) {
            $payload = $event->payload;
        } elseif (method_exists($event, 'toArray')) {
            $payload = $event->toArray();
        } else {
            // Use reflection to get public properties
            $reflection = new \ReflectionClass($event);
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
                $payload[$prop->getName()] = $prop->getValue($event);
            }
        }

        $this->engine->handleEvent($eventName, $payload, $tenant);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        // Subscribe to relevant system events
        $eventsToListen = [
            'App\Events\SystemOfflineDetected',
            'App\Events\SecurityThreatDetected',
            'App\Events\BackupCompleted',
            'App\Events\UpdateCompleted',
            'App\Events\ConfigurationChanged',
            'App\Events\LicenseChanged',
            'App\Events\SubscriptionExpired',
            'App\Events\QuotaExceeded',
            'App\Events\AlertCreated',
            'App\Events\AuditCreated',
        ];

        foreach ($eventsToListen as $eventClass) {
            $events->listen($eventClass, [HQWorkflowEventSubscriber::class, 'handleSystemEvent']);
        }
    }
}
