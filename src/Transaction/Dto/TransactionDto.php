<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

use App\Transaction\Enum\TransactionType;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class TransactionDto
{
    public function __construct(
        private Uuid $id,
        private Uuid $sender,
        private Uuid $recipient,
        private float $amount,
        private string $currency,
        private TransactionType $type,
        private DateTimeImmutable $createdAt,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSender(): Uuid
    {
        return $this->sender;
    }

    public function getRecipient(): Uuid
    {
        return $this->recipient;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
