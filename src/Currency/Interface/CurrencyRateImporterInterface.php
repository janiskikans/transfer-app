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
     * @param Currency[]|null $currencies
     * @return CurrencyRateImportData[]
     * @throws CurrencyRateImporterException
     */
    public function importRates(Currency $source, ?array $currencies = null): array;
}
