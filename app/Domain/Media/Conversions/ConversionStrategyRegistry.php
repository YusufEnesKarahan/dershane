<?php
namespace App\Domain\Media\Conversions;

class ConversionStrategyRegistry
{
    protected array $strategies = [];

    public function register(ConversionStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    public function getStrategies(): array
    {
        return $this->strategies;
    }
}
