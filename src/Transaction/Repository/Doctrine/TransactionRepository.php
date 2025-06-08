<?php

declare(strict_types=1);

namespace App\Transaction\Repository\Doctrine;

use App\Transaction\Entity\Transaction;
use App\Transaction\Repository\TransactionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class TransactionRepository implements TransactionRepositoryInterface
{
    /** @var EntityRepository<Transaction> */
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(Transaction::class);
    }

    public function getByAccountId(string $accountId, int $offset = 0, int $limit = 100): array
    {
        return $this->repository->createQueryBuilder('t')
            ->where('t.sender = :accountId OR t.recipient = :accountId')
            ->setParameter('accountId', $accountId)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Transaction $transaction): void
    {
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }
}
