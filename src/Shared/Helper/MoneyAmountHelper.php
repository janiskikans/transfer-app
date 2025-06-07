<?php

declare(strict_types=1);

namespace App\Shared\Helper;

class MoneyAmountHelper
{
    public static function convertToMajor(int $amountInMinor, int $decimalPlaces): float
    {
        return $amountInMinor / (10 ** $decimalPlaces);
    }

    public static function convertToMinor(float $amountInMajor, int $decimalPlaces): int
    {
        return (int)($amountInMajor * (10 ** $decimalPlaces));
    }
}
