<?php

declare(strict_types=1);

namespace App\Tests\DummyFactory\Transaction;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Transaction\Entity\Transaction;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class TransactionFactory
{
    public static function create(
        int $amount = 1000,
        ?Account $senderAccount = null,
        ?Account $recipientAccount = null,
        ?Currency $currency = null,
        DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ): Transaction {
        return new Transaction(
            id: Uuid::v6(),
            sender: $senderAccount ?? AccountFactory::create(),
            recipient: $recipientAccount ?? AccountFactory::create(),
            amount: $amount,
            currency: $currency ?? CurrencyFactory::create(),
            createdAt: $createdAt,
        );
    }
}
