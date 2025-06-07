<?php

declare(strict_types=1);

namespace App\Currency\Structure;

use App\Currency\Enum\Currency;

// TODO: Dto
readonly class CurrencyRateImportData
{
    public function __construct(
        private Currency $baseCurrency,
        private Currency $targetCurrency,
        private float $rate,
    ) {
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
