<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Repository\CurrencyRateRepositoryInterface;

readonly class CurrencyConversionService
{
    public function __construct(private CurrencyRateRepositoryInterface $currencyRateRepository)
    {
    }

    /**
     * @throws CurrencyRateNotFoundException
     * // TODO: Maybe make source configurable?
     */
    public function convert(
        int $amount,
        Currency $baseCurrency,
        Currency $targetCurrency,
        CurrencyRateSource $source = CurrencyRateSource::EXCHANGE_RATE_HOST
    ): int {
        if ($baseCurrency === $targetCurrency) {
            return $amount;
        }

        $conversionRate = $this->currencyRateRepository->getRate($baseCurrency, $targetCurrency, $source);
        if (!$conversionRate) {
            throw new CurrencyRateNotFoundException();
        }

        return (int)($amount * $conversionRate->getRate());
    }
}
