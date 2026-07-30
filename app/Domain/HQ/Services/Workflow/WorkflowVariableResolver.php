<?php

namespace App\Domain\HQ\Services\Workflow;

use Illuminate\Support\Arr;

class WorkflowVariableResolver
{
    /**
     * Resolve a dynamic variable like "{{ payload.tenant_id }}" from the payload.
     * If the value does not contain placeholders, returns it directly.
     */
    public function resolveValue($value, array $payload)
    {
        if (!is_string($value)) {
            return $value;
        }

        // If the whole string is exactly one placeholder e.g. "{{ user.id }}",
        // we can return the exact type (int, array, etc.) instead of interpolating into a string.
        if (preg_match('/^{{\s*([a-zA-Z0-9_.-]+)\s*}}$/', $value, $matches)) {
            return Arr::get($payload, $matches[1]);
        }

        // Otherwise replace all occurrences of {{ key }} within the string
        return preg_replace_callback('/{{\s*([a-zA-Z0-9_.-]+)\s*}}/', function ($matches) use ($payload) {
            $resolved = Arr::get($payload, $matches[1]);
            if (is_array($resolved) || is_object($resolved)) {
                return json_encode($resolved);
            }
            return $resolved;
        }, $value);
    }
}
