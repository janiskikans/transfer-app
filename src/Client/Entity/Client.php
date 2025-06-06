<?php

declare(strict_types=1);

namespace App\Client\Entity;

use App\Account\Entity\Account;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'client')]
class Client
{
    /** @var Collection<array-key, Account> */
    #[OneToMany(targetEntity: Account::class, mappedBy: 'client')]
    private Collection $accounts;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, unique: true)]
        #[GeneratedValue(strategy: 'CUSTOM')]
        #[CustomIdGenerator(class: UuidGenerator::class)]
        private Uuid $id,
        #[Column(type: 'string', length: 180, unique: true)]
        private readonly string $email,
    ) {
        $this->accounts = new ArrayCollection();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    /** @return Collection<array-key, Account> */
    public function accounts(): Collection
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
