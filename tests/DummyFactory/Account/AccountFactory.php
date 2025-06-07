<?php

declare(strict_types=1);

namespace App\Tests\DummyFactory\Account;

use App\Account\Entity\Account;
use App\Client\Entity\Client;
use App\Currency\Entity\Currency;
use App\Tests\DummyFactory\Client\ClientFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class AccountFactory
{
    public static function create(
        int $balance = 10000,
        ?Client $client = null,
        ?Currency $currency = null,
        DateTimeImmutable $createdAt = new DateTimeImmutable()
    ): Account {
        return new Account(
            id: Uuid::v6(),
            client: $client ?? ClientFactory::create(),
            currency: $currency ?? CurrencyFactory::create(),
            balance: $balance,
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );
    }
}
