<?php

declare(strict_types=1);

namespace App\Account\Entity;

use App\Account\Exceptions\AccountAddClientFailedException;
use App\Client\Entity\Client;
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
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'account')]
class Account
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, unique: true)]
        #[GeneratedValue(strategy: 'CUSTOM')]
        #[CustomIdGenerator(class: UuidGenerator::class)]
        private Uuid $id,
        #[ManyToOne(targetEntity: Client::class, inversedBy: 'accounts')]
        private Client $client,
        #[ManyToOne(targetEntity: Currency::class)]
        #[JoinColumn(name: 'currency', referencedColumnName: 'code', nullable: false)]
        private Currency $currency,
        #[Column(type: 'integer')]
        private int $balance,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'create')]
        private readonly DateTimeImmutable $createdAt,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'update')]
        private readonly DateTimeImmutable $updatedAt,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    #[Ignore]
    public function getClient(): Client
    {
        return $this->client;
    }

    public function getClientId(): Uuid
    {
        return $this->client->getId();
    }

    #[Ignore]
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCurrencyCode(): string
    {
        return $this->currency->code();
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
}
