<?php

namespace App\Listeners;

use App\Events\SecurityAnomalyDetected;
use App\Events\HealthCheckFailed;
use App\Events\MetricThresholdExceeded;
use App\Events\PerformanceDegraded;
use App\Domain\HQ\Services\HQAlertService;

class ObservabilityAlertListener
{
    protected $alertService;

    public function __construct(HQAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function handleSecurityAnomaly(SecurityAnomalyDetected $event): void
    {
        $this->alertService->createAlert(
            severity: $event->securityEvent->severity,
            title: 'Security Anomaly Detected',
            message: "Anomaly type: {$event->securityEvent->event_type} detected.",
            tenantId: $event->securityEvent->tenant_id,
            metadata: $event->securityEvent->metadata ?? []
        );
    }

    public function handleHealthCheckFailed(HealthCheckFailed $event): void
    {
        $this->alertService->createAlert(
            severity: $event->healthCheck->status, // warning or critical
            title: "Health Check Failed: {$event->healthCheck->component}",
            message: "Component {$event->healthCheck->component} reported {$event->healthCheck->status}.",
            tenantId: $event->healthCheck->tenant_id,
            metadata: $event->healthCheck->details ?? []
        );
    }

    public function handleMetricThresholdExceeded(MetricThresholdExceeded $event): void
    {
        $this->alertService->createAlert(
            severity: 'warning',
            title: "Metric Threshold Exceeded: {$event->metric->metric_name}",
            message: "Metric {$event->metric->metric_name} value {$event->metric->value} exceeded threshold {$event->threshold}.",
            tenantId: $event->metric->tenant_id,
            metadata: $event->metric->tags ?? []
        );
    }

    public function handlePerformanceDegraded(PerformanceDegraded $event): void
    {
        $this->alertService->createAlert(
            severity: 'warning',
            title: "Performance Degraded in {$event->trace->service_name}",
            message: "Operation {$event->trace->operation} took {$event->trace->duration_ms}ms. Reason: {$event->reason}.",
            tenantId: $event->trace->tenant_id,
            metadata: $event->trace->metadata ?? []
        );
    }
}
