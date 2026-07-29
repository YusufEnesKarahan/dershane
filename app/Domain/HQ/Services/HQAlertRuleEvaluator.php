<?php

namespace App\Domain\HQ\Services;

use App\Models\HQAlertRule;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use App\Models\HQAlert;

class HQAlertRuleEvaluator
{
    protected HQAlertService $alertService;

    public function __construct(HQAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Evaluate rules for a specific event type.
     */
    public function evaluateEvent(string $eventType, array $context)
    {
        $rules = HQAlertRule::where('event_type', $eventType)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            $this->evaluateRule($rule, $context);
        }
    }

    /**
     * Evaluate a single rule against a context payload.
     */
    protected function evaluateRule(HQAlertRule $rule, array $context)
    {
        // Simple condition matching based on the JSON condition
        $condition = $rule->condition;
        $matched = false;

        // Implement logic based on event type and condition
        switch ($rule->event_type) {
            case 'license.expired':
            case 'license.expiring':
                $matched = true; // For now, if the event triggered, the rule matches
                break;
            case 'system.offline':
                $matched = true;
                break;
            case 'backup.failed':
                $matched = true;
                break;
            case 'security.threat':
                $matched = true;
                break;
            case 'update.failed':
                $matched = true;
                break;
            default:
                // Basic matching for other types
                if (isset($condition['type']) && isset($context['type']) && $condition['type'] === $context['type']) {
                    $matched = true;
                }
                break;
        }

        if ($matched && !$this->isInCooldown($rule, $context)) {
            $this->triggerAlert($rule, $context);
        }
    }

    /**
     * Check if a rule is in cooldown for the specific tenant/system.
     */
    protected function isInCooldown(HQAlertRule $rule, array $context): bool
    {
        if ($rule->cooldown_minutes <= 0) {
            return false;
        }

        $tenantId = $context['tenant_id'] ?? null;
        $systemInstanceId = $context['system_instance_id'] ?? null;

        $lastAlertQuery = HQAlert::where('rule_id', $rule->id);

        if ($tenantId) {
            $lastAlertQuery->where('tenant_id', $tenantId);
        }
        if ($systemInstanceId) {
            $lastAlertQuery->where('system_instance_id', $systemInstanceId);
        }

        $lastAlert = $lastAlertQuery->latest('triggered_at')->first();

        if ($lastAlert) {
            $cooldownTime = $lastAlert->triggered_at->addMinutes($rule->cooldown_minutes);
            return now()->lessThan($cooldownTime);
        }

        return false;
    }

    /**
     * Trigger an alert based on a rule and context.
     */
    protected function triggerAlert(HQAlertRule $rule, array $context)
    {
        $title = $rule->name;
        $message = "Alert triggered for rule: {$rule->name}";

        if (isset($context['message'])) {
            $message .= "\nDetails: " . $context['message'];
        }

        $tenantId = $context['tenant_id'] ?? null;
        $systemInstanceId = $context['system_instance_id'] ?? null;

        $this->alertService->createAlert(
            severity: $rule->severity,
            title: $title,
            message: $message,
            ruleId: $rule->id,
            tenantId: $tenantId,
            systemInstanceId: $systemInstanceId,
            metadata: $context['metadata'] ?? []
        );
    }
}
