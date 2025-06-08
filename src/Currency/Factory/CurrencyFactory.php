<?php

namespace App\Currency\Factory;

use App\Currency\Entity\Currency;
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
            'code' => self::faker()->currencyCode(),
            'decimalPlaces' => 2,
            'name' => self::faker()->currencyCode(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Currency $currency): void {})
            ;
    }
}
