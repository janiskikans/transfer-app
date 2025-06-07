<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Interface\CurrencyRateImporterInterface;
use App\Currency\Structure\CurrencyRateImportResult;

readonly class CurrencyRateImportService
{
    public function __construct(
        private CurrencyRateImporterInterface $rateImporter,
    ) {
    }

    public function importAndSaveRates(): CurrencyRateImportResult
    {
        $rates = $this->rateImporter->importRates(Currency::USD, [Currency::USD, Currency::EUR, Currency::GBP]);

        dump($rates);

        return new CurrencyRateImportResult(count($rates));
    }
}
