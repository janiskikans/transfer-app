<?php

namespace App\Currency\Interface;

use App\Currency\Dto\CurrencyRateImportData;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;

interface CurrencyRateImporterInterface
{
    public function getSource(): CurrencyRateSource;

    /**
     * @param Currency[]|null $targetCurrencies
     * @return CurrencyRateImportData[]
     * @throws CurrencyRateImporterException
     */
    public function importRates(Currency $sourceCurrency, ?array $targetCurrencies = null): array;
}
