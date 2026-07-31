<?php

namespace App\Domain\HQ\Services\Governance;

use App\Models\HQPolicy;
use App\Models\HQPolicyAssignment;
use App\Models\HQTenant;
use Illuminate\Support\Arr;

class PolicyEngineService
{
    /**
     * Assign a policy to a tenant (or globally if tenant_id is null)
     */
    public function assign(HQPolicy $policy, ?HQTenant $tenant = null, array $overrides = []): HQPolicyAssignment
    {
        return HQPolicyAssignment::updateOrCreate(
            [
                'policy_id' => $policy->id,
                'tenant_id' => $tenant ? $tenant->id : null,
            ],
            [
                'overrides' => $overrides,
            ]
        );
    }

    /**
     * Unassign a policy
     */
    public function unassign(HQPolicy $policy, ?HQTenant $tenant = null): bool
    {
        return (bool) HQPolicyAssignment::where('policy_id', $policy->id)
            ->where('tenant_id', $tenant ? $tenant->id : null)
            ->delete();
    }

    /**
     * Validate JSON policy structure
     */
    public function validate(array $logic): bool
    {
        // Simple validation logic to ensure no harmful constructs
        // We only allow "all", "any" as top-level keys, and "metric", "operator", "value" as leaves
        if (empty($logic)) {
            return false;
        }

        return $this->recursiveValidate($logic);
    }

    protected function recursiveValidate(array $node): bool
    {
        if (isset($node['all']) && is_array($node['all'])) {
            foreach ($node['all'] as $child) {
                if (!$this->recursiveValidate($child)) return false;
            }
            return true;
        }

        if (isset($node['any']) && is_array($node['any'])) {
            foreach ($node['any'] as $child) {
                if (!$this->recursiveValidate($child)) return false;
            }
            return true;
        }

        // Must be a leaf node
        if (isset($node['metric']) && isset($node['operator']) && array_key_exists('value', $node)) {
            $allowedOperators = ['==', '!=', '>', '<', '>=', '<=', 'in', 'contains'];
            if (!in_array($node['operator'], $allowedOperators)) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Evaluate a policy against a given context. Context holds key-value metric data.
     */
    public function evaluate(HQPolicy $policy, array $context): bool
    {
        if (empty($policy->logic)) {
            return true; // Empty policy is effectively a pass or no-op
        }

        $result = $this->evaluateNode($policy->logic, $context);

        if ($result) {
            event(new \App\Events\PolicyPassed($policy, $context));
        } else {
            event(new \App\Events\PolicyFailed($policy, $context));
        }

        return $result;
    }

    protected function evaluateNode(array $node, array $context): bool
    {
        if (isset($node['all']) && is_array($node['all'])) {
            foreach ($node['all'] as $child) {
                if (!$this->evaluateNode($child, $context)) {
                    return false;
                }
            }
            return true;
        }

        if (isset($node['any']) && is_array($node['any'])) {
            foreach ($node['any'] as $child) {
                if ($this->evaluateNode($child, $context)) {
                    return true;
                }
            }
            return false;
        }

        return $this->evaluateLeaf($node, $context);
    }

    protected function evaluateLeaf(array $leaf, array $context): bool
    {
        $metric = $leaf['metric'] ?? null;
        $operator = $leaf['operator'] ?? null;
        $expectedValue = $leaf['value'] ?? null;

        if (!$metric || !$operator || !array_key_exists($metric, $context)) {
            return false;
        }

        $actualValue = $context[$metric];

        return match ($operator) {
            '==' => $actualValue == $expectedValue,
            '!=' => $actualValue != $expectedValue,
            '>' => $actualValue > $expectedValue,
            '<' => $actualValue < $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            'in' => is_array($expectedValue) && in_array($actualValue, $expectedValue),
            'contains' => is_string($actualValue) && str_contains($actualValue, (string) $expectedValue),
            default => false,
        };
    }
}
