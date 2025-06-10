<?php

namespace App\Currency\Repository;

use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;

interface CurrencyRateRepositoryInterface
{
    public function getRate(CurrencyCode $baseCurrency, CurrencyCode $targetCurrency, CurrencyRateSource $source): ?CurrencyRate;
}
