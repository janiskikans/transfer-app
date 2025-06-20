<?php

declare(strict_types=1);

namespace App\Account\Entity;

use App\Account\Exceptions\AccountAddClientFailedException;
use App\Account\Exceptions\InsufficientBalanceException;
use App\Client\Entity\Client;
use App\Currency\Entity\Currency;
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
#[Table(name: 'account')]
#[Index(name: 'account_client_id_idx', columns: ['client_id'])]
#[Index(name: 'account_currency_idx', columns: ['currency'])]
#[Index(name: 'account_created_at_idx', columns: ['created_at'])]
class Account
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, unique: true)]
        #[GeneratedValue(strategy: 'CUSTOM')]
        #[CustomIdGenerator(class: UuidGenerator::class)]
        private ?Uuid $id = null,
        #[ManyToOne(targetEntity: Client::class, inversedBy: 'accounts')]
        private ?Client $client = null,
        #[ManyToOne(targetEntity: Currency::class)]
        #[JoinColumn(name: 'currency', referencedColumnName: 'code', nullable: false)]
        private ?Currency $currency = null,
        #[Column(type: 'integer')]
        private ?int $balance = null,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'create')]
        private readonly ?DateTimeImmutable $createdAt = null,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'update')]
        private ?DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setClient(Client $client): void
    {
        if (isset($this->client) && $this->client === $client) {
            throw new AccountAddClientFailedException('Client already is set');
        }

        $this->client = $client;
    }

    /**
     * @throws InsufficientBalanceException
     */
    public function debit(int $amount): self
    {
        if ($this->balance < $amount) {
            throw new InsufficientBalanceException();
        }

        $this->balance -= $amount;

        return $this;
    }

    public function credit(int $amount): self
    {
        $this->balance += $amount;

        return $this;
    }
}
