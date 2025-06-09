<?php

declare(strict_types=1);

namespace App\Tests\DummyFactory\Currency;

use App\Currency\Entity\Currency;
use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\CurrencyRateSource;

class CurrencyRateFactory
{
    public static function create(
        float $rate = 1.23,
        ?Currency $baseCurrency = null,
        ?Currency $targetCurrency = null,
    ): CurrencyRate {
        return new CurrencyRate(
            id: 1,
            source: CurrencyRateSource::FAKE,
            baseCurrency: $baseCurrency ?? CurrencyFactory::create(),
            targetCurrency: $targetCurrency ?? CurrencyFactory::create('EUR', 'Euro'),
            rate: $rate,
        );
    }
}
