<?php

namespace App\Currency\Interface;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;
use App\Currency\Structure\CurrencyRateImportData;

interface CurrencyRateImporterInterface
{
    public function getSource(): CurrencyRateSource;

    /**
     * @param Currency[] $currencies
     * @return CurrencyRateImportData[]
     * @throws CurrencyRateImporterException
     */
    public function importRates(Currency $source, array $currencies): array;
}
