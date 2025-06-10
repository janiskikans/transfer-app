<?php

namespace App\Currency\Interface;

use App\Currency\Dto\CurrencyRateImportData;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;

interface CurrencyRateImporterInterface
{
    public function getSource(): CurrencyRateSource;

    /**
     * @param CurrencyCode[]|null $targetCurrencies
     * @return CurrencyRateImportData[]
     * @throws CurrencyRateImporterException
     */
    public function importRates(CurrencyCode $sourceCurrency, ?array $targetCurrencies = null): array;
}
