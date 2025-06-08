<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Dto\CurrencyRateImportData;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Interface\CurrencyRateImporterInterface;

/**
 * Importer that returns currency rates from a predefined list. For testing purposes only.
 */
class FakeCurrencyRateImporter implements CurrencyRateImporterInterface
{
    public function getSource(): CurrencyRateSource
    {
        return CurrencyRateSource::FAKE;
    }

    /**
     * @param Currency[] $currencies
     * @return CurrencyRateImportData[]
     */
    public function importRates(Currency $source, ?array $currencies = null): array
    {
        $data = [
            ['USD', 'EUR', 0.88],
            ['USD', 'GBP', 0.74],
            ['EUR', 'USD', 1.14],
            ['GBP', 'USD', 1.35],
        ];

        $rates = [];

        foreach ($data as $row) {
            $rates[] = new CurrencyRateImportData(
                Currency::from($row[0]),
                Currency::from($row[1]),
                $row[2],
            );
        }

        return $rates;
    }
}
