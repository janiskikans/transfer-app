<?php

declare(strict_types=1);

namespace App\Transaction\Repository\Doctrine;

use App\Transaction\Entity\Transaction;
use App\Transaction\Repository\TransactionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository implements TransactionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getByAccountId(string $accountId, int $offset = 0, int $limit = 100): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.sender = :accountId OR t.recipient = :accountId')
            ->setParameter('accountId', $accountId)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getById(string $id): ?Transaction
    {
        return $this->findOneBy(['id' => $id]);
    }
}
