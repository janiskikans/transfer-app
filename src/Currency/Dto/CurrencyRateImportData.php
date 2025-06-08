<?php

declare(strict_types=1);

namespace App\Currency\Dto;

use App\Currency\Enum\Currency;

readonly class CurrencyRateImportData
{
    public function __construct(
        public Currency $baseCurrency,
        public Currency $targetCurrency,
        public float $rate,
    ) {
    }
}
