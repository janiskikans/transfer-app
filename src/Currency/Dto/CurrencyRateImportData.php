<?php

declare(strict_types=1);

namespace App\Currency\Dto;

use App\Currency\Enum\CurrencyCode;

readonly class CurrencyRateImportData
{
    public function __construct(
        public CurrencyCode $baseCurrency,
        public CurrencyCode $targetCurrency,
        public float $rate,
    ) {
    }
}
