<?php

declare(strict_types=1);

namespace App\Transaction\Entity;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;
use App\Shared\Helper\MoneyAmountHelper;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'transaction')]
#[Index(name: 'transaction_created_at_idx', columns: ['created_at'])]
class Transaction
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, unique: true)]
        #[GeneratedValue(strategy: 'CUSTOM')]
        #[CustomIdGenerator(class: UuidGenerator::class)]
        private ?Uuid $id = null,
        #[ManyToOne(targetEntity: Account::class, inversedBy: 'sentTransactions')]
        #[JoinColumn(name: 'sender', referencedColumnName: 'id', nullable: false)]
        private ?Account $sender = null,
        #[ManyToOne(targetEntity: Account::class, inversedBy: 'receivedTransactions')]
        #[JoinColumn(name: 'recipient', referencedColumnName: 'id', nullable: false)]
        private ?Account $recipient = null,
        #[Column(type: 'integer')]
        private ?int $amount = null,
        #[ManyToOne(targetEntity: Currency::class)]
        #[JoinColumn(name: 'currency', referencedColumnName: 'code', nullable: false)]
        private ?Currency $currency = null,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'create')]
        private ?DateTimeImmutable $createdAt = null,
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

    public function getAmountAsFloat(): float
    {
        return MoneyAmountHelper::convertToMajor($this->amount, $this->recipient->getCurrency()->getDecimalPlaces());
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
