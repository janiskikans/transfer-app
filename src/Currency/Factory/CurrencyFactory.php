<?php

namespace App\Currency\Factory;

use App\Currency\Entity\Currency;
use App\Currency\Enum\CurrencyCode;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @codeCoverageIgnore
 * @extends PersistentProxyObjectFactory<Currency>
 */
final class CurrencyFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Currency::class;
    }

    protected function defaults(): array | callable
    {
        return [
            'code' => CurrencyCode::tryFrom(self::faker()->currencyCode()) ?? CurrencyCode::USD,
            'decimalPlaces' => 2,
            'name' => self::faker()->currencyCode(),
        ];
    }
}
