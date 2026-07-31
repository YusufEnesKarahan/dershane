<?php

namespace App\Domain\HQ\Services\Configuration;

use App\Models\HQFeatureFlag;
use App\Models\HQTenant;

class FeatureFlagService
{
    /**
     * Check if a feature is enabled for a specific context.
     * Evaluates the recursive JSON rules natively.
     */
    public function isEnabled(string $key, array $context = []): bool
    {
        $flag = HQFeatureFlag::where('key', $key)->first();
        
        if (!$flag) {
            return false;
        }

        // 1. Check Overrides (Targets)
        if (isset($context['tenant_id'])) {
            $target = $flag->targets()->where('target_type', 'tenant')->where('target_id', $context['tenant_id'])->first();
            if ($target) {
                return $target->is_enabled;
            }
        }

        // 2. Check Master Switch
        if (!$flag->is_enabled) {
            return false;
        }

        // 3. Evaluate JSON Rules (Zero RCE, Recursive)
        if (empty($flag->rules)) {
            return true; // Enabled globally with no restrictive rules
        }

        return $this->evaluateNode($flag->rules, $context);
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
        // Handle Percentage Rollout
        if (isset($leaf['percentage'])) {
            // We need a stable identifier to hash, e.g., tenant_id or user_id
            $identifier = $context['tenant_id'] ?? $context['user_id'] ?? rand(1, 1000);
            // Simple consistent hashing
            $hash = crc32((string) $identifier) % 100;
            return $hash < (int) $leaf['percentage'];
        }

        // Handle Subscription matching
        if (isset($leaf['subscription'])) {
            $actualSub = $context['subscription'] ?? 'free';
            return $actualSub === $leaf['subscription'];
        }
        
        // Custom logical matching like ==, >, <
        if (isset($leaf['metric']) && isset($leaf['operator']) && array_key_exists('value', $leaf)) {
            $metric = $leaf['metric'];
            if (!isset($context[$metric])) return false;
            
            $actual = $context[$metric];
            $expected = $leaf['value'];
            
            return match($leaf['operator']) {
                '==' => $actual == $expected,
                '!=' => $actual != $expected,
                '>'  => $actual > $expected,
                '<'  => $actual < $expected,
                default => false
            };
        }

        return false;
    }
}
