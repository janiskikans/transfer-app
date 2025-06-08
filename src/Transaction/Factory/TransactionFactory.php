<?php

declare(strict_types=1);

namespace App\Transaction\Factory;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;
use App\Currency\Enum\Currency as CurrencyEnum;
use App\Transaction\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

readonly class TransactionFactory
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @throws ORMException
     */
    public function create(
        Account $sender,
        Account $recipient,
        int $amount,
        CurrencyEnum $currency,
    ): Transaction {
        return new Transaction(
            sender: $sender,
            recipient: $recipient,
            amount: $amount,
            currency: $this->em->getReference(Currency::class, $currency->value),
        );
    }
}
