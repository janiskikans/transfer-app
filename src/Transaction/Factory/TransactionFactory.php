<?php

declare(strict_types=1);

namespace App\Transaction\Factory;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;
use App\Transaction\Entity\Transaction;

readonly class TransactionFactory
{
    public function create(
        Account $sender,
        Account $recipient,
        int $amount,
        Currency $currency,
    ): Transaction {
        return new Transaction(
            sender: $sender,
            recipient: $recipient,
            amount: $amount,
            currency: $currency,
        );
    }
}
