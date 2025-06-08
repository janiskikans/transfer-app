<?php

declare(strict_types=1);

namespace App\Currency\Dto;

class CurrencyRateImportResult
{
    public function __construct(private int $newCount = 0, private int $updatedCount = 0)
    {
    }

    public function getNewCount(): int
    {
        return $this->newCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function onUpdatedRate(): self
    {
        $this->updatedCount++;

        return $this;
    }

    public function onNewRate(): self
    {
        $this->newCount++;

        return $this;
    }
}
