<?php

namespace App\Domain\HQ\Services\Workflow;

class WorkflowConditionService
{
    protected WorkflowVariableResolver $resolver;

    public function __construct(WorkflowVariableResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Evaluate a group of conditions which can be nested.
     * Expects config like:
     * [
     *   'operator' => 'AND',
     *   'rules' => [
     *      ['field' => 'status', 'operator' => 'equals', 'value' => 'offline'],
     *      [
     *         'operator' => 'OR',
     *         'rules' => [...]
     *      ]
     *   ]
     * ]
     */
    public function evaluateGroups(array $conditions, array $payload): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $operator = strtoupper($conditions['operator'] ?? 'AND');
        $rules = $conditions['rules'] ?? [];

        if (empty($rules)) {
            return true;
        }

        foreach ($rules as $rule) {
            $isGroup = isset($rule['rules']);
            
            if ($isGroup) {
                $result = $this->evaluateGroups($rule, $payload);
            } else {
                $result = $this->evaluateRule($rule, $payload);
            }

            if ($operator === 'AND' && !$result) {
                return false;
            }
            if ($operator === 'OR' && $result) {
                return true;
            }
        }

        return $operator === 'AND'; // If AND, all must be true. If OR and we are here, all were false.
    }

    /**
     * Evaluate a single rule.
     */
    public function evaluateRule(array $rule, array $payload): bool
    {
        $fieldValue = $this->resolver->resolveValue($rule['field'] ?? '', $payload);
        $expectedValue = $this->resolver->resolveValue($rule['value'] ?? '', $payload);
        $operator = $rule['operator'] ?? 'equals';

        return match ($operator) {
            'equals' => $fieldValue == $expectedValue,
            'not_equals' => $fieldValue != $expectedValue,
            'greater_than' => $fieldValue > $expectedValue,
            'less_than' => $fieldValue < $expectedValue,
            'contains' => is_string($fieldValue) && str_contains($fieldValue, (string)$expectedValue),
            'starts_with' => is_string($fieldValue) && str_starts_with($fieldValue, (string)$expectedValue),
            'ends_with' => is_string($fieldValue) && str_ends_with($fieldValue, (string)$expectedValue),
            'in_array' => is_array($expectedValue) && in_array($fieldValue, $expectedValue),
            'not_in_array' => is_array($expectedValue) && !in_array($fieldValue, $expectedValue),
            default => false,
        };
    }
}
