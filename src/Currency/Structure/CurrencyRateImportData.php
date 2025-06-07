<?php

declare(strict_types=1);

namespace App\Currency\Structure;

readonly class CurrencyRateImportData
{
    public function __construct(
        private string $baseCurrency,
        private string $targetCurrency,
        private float $rate,
    ) {
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
