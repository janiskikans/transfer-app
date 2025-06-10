<?php

namespace App\Currency\Repository;

use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;

interface CurrencyRateRepositoryInterface
{
    public function getRate(Currency $baseCurrency, Currency $targetCurrency, CurrencyRateSource $source): ?CurrencyRate;
}
