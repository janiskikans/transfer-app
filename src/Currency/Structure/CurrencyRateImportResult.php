<?php

declare(strict_types=1);

namespace App\Currency\Structure;

readonly class CurrencyRateImportResult
{
    public function __construct(private int $importedCount = 0)
    {
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
