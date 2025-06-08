<?php

namespace App\Account\Factory;

use App\Account\Entity\Account;
use App\Currency\Factory\CurrencyFactory;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @codeCoverageIgnore
 * @extends PersistentProxyObjectFactory<Account>
 */
final class AccountFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Account::class;
    }

    protected function defaults(): array|callable
    {
        $now = new \DateTimeImmutable();

        return [
            'id' => Uuid::v6(),
            'balance' => 0,
            'createdAt' => $now,
            'updatedAt' => $now,
            'currency' => CurrencyFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Account $account): void {})
        ;
    }
}
