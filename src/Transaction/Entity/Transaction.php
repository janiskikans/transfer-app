<?php

declare(strict_types=1);

namespace App\Transaction\Entity;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'transaction')]
class Transaction
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, unique: true)]
        #[GeneratedValue(strategy: 'CUSTOM')]
        #[CustomIdGenerator(class: UuidGenerator::class)]
        private Uuid $id,
        #[ManyToOne(targetEntity: Account::class, inversedBy: 'sentTransactions')]
        #[JoinColumn(name: 'sender', referencedColumnName: 'id', nullable: false)]
        private Account $sender,
        #[ManyToOne(targetEntity: Account::class, inversedBy: 'receivedTransactions')]
        #[JoinColumn(name: 'recipient', referencedColumnName: 'id', nullable: false)]
        private Account $recipient,
        #[Column(type: 'integer')]
        private int $amount,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'create')]
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSender(): Account
    {
        return $this->sender;
    }

    public function getSenderId(): Uuid
    {
        return $this->sender->getId();
    }

    public function getRecipient(): Account
    {
        return $this->recipient;
    }

    public function getRecipientId(): Uuid
    {
        return $this->recipient->getId();
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->recipient->getCurrency();
    }

    public function getAmountAsFloat(): float
    {
        // TODO: Helper?
        return $this->amount / (10 ** $this->recipient->getCurrency()->getDecimalPlaces());
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
