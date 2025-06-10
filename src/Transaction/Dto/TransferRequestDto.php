<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;

final readonly class TransferRequestDto
{
    public function __construct(
        private Account $sender,
        private Account $recipient,
        private int $amount,
        private Currency $currency,
    ) {
    }

    public function getSender(): Account
    {
        return $this->sender;
    }

    public function getRecipient(): Account
    {
        return $this->recipient;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
