<?php

declare(strict_types=1);

namespace App\Transaction\Factory;

use App\Account\Entity\Account;
use App\Transaction\Dto\TransactionDto;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionType;

class TransactionDtoFactory
{
    public function createFromEntity(Transaction $transaction, Account $lookupAccount): TransactionDto
    {
        $transactionType = $transaction->getSender()->getId() === $lookupAccount->getId()
            ? TransactionType::DEBIT
            : TransactionType::CREDIT;

        return new TransactionDto(
            $transaction->getId(),
            $transaction->getSender()->getId(),
            $transaction->getRecipient()->getId(),
            $transaction->getAmountAsFloat(),
            $transaction->getCurrency()->getCode(),
            $transactionType,
            $transaction->getCreatedAt(),
        );
    }
}
