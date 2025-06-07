<?php

declare(strict_types=1);

namespace App\Account\Repository\Doctrine;

use App\Account\Entity\Account;
use App\Account\Repository\AccountRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class AccountRepository implements AccountRepositoryInterface
{
    /** @var EntityRepository<Account> */
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(Account::class);
    }

    /**
     * @return Account[]
     */
    public function getByClientId(string $clientId): array
    {
        return $this->repository->findBy(['client' => $clientId]);
    }

    public function getById(string $id): ?Account
    {
        return $this->repository->findOneBy(['id' => $id]);
    }
}
