<?php

declare(strict_types=1);

namespace App\Account\Repository\Doctrine;

use App\Account\Entity\Account;
use App\Account\Repository\AccountRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountRepository extends ServiceEntityRepository implements AccountRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @return Account[]
     */
    public function getByClientId(string $clientId): array
    {
        return $this->findBy(['client' => $clientId]);
    }

    public function getById(string $id): ?Account
    {
        return $this->findOneBy(['id' => $id]);
    }
}
