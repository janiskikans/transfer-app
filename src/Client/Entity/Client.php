<?php

declare(strict_types=1);

namespace App\Client\Entity;

use App\Account\Entity\Account;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'client')]
class Client
{
    /** @var Collection<array-key, Account> */
    #[OneToMany(targetEntity: Account::class, mappedBy: 'client', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $accounts;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, unique: true)]
        #[GeneratedValue(strategy: 'CUSTOM')]
        #[CustomIdGenerator(class: UuidGenerator::class)]
        private Uuid $id,
        #[Column(type: 'string', length: 180, unique: true)]
        private readonly string $email,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'create')]
        private readonly DateTimeImmutable $createdAt,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'update')]
        private readonly DateTimeImmutable $updatedAt,
    ) {
        $this->accounts = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return Collection<array-key, Account> */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): void
    {
        if ($this->accounts->contains($account)) {
            return;
        }

        $this->accounts->add($account);
        $account->setClient($this);
    }
}
