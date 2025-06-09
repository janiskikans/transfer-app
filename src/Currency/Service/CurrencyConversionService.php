<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Currency\Repository\CurrencyRepositoryInterface;

readonly class CurrencyConversionService
{
    public function __construct(
        private CurrencyRateRepositoryInterface $currencyRateRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    /**
     * @throws CurrencyRateNotFoundException
     */
    public function convert(
        int $amount,
        Currency $baseCurrency,
        Currency $targetCurrency,
        CurrencyRateSource $source = CurrencyRateSource::FAKE,
    ): int {
        if ($baseCurrency === $targetCurrency) {
            return $amount;
        }

        $conversionRate = $this->currencyRateRepository->getRate($baseCurrency, $targetCurrency, $source);
        if (!$conversionRate) {
            throw new CurrencyRateNotFoundException(
                sprintf(
                    'Currency rate not found for base currency: %s, target currency: %s',
                    $baseCurrency->value,
                    $targetCurrency->value,
                )
            );
        }

        $baseMinorUnitFactor = $this->getMinorUnitFactor($baseCurrency);
        $targetMinorUnitFactor = $this->getMinorUnitFactor($targetCurrency);

        return (int)($amount * ($targetMinorUnitFactor / $baseMinorUnitFactor) * $conversionRate->getRate());
    }

    private function getMinorUnitFactor(Currency $currency): int
    {
        $currencyEntity = $this->currencyRepository->getByCode($currency->value);;

        return 10 ** $currencyEntity->getDecimalPlaces();
    }
}
