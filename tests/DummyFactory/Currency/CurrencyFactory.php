<?php

declare(strict_types=1);

namespace App\Tests\DummyFactory\Currency;

use App\Currency\Entity\Currency;
use App\Currency\Enum\CurrencyCode;

class CurrencyFactory
{
    public static function create(
        string $code = 'USD',
        string $name = 'US Dollar',
        int $decimalPlaces = 2,
    ): Currency {
        return new Currency(
            code: CurrencyCode::from($code),
            name: $name,
            decimalPlaces: $decimalPlaces,
        );
    }
}
