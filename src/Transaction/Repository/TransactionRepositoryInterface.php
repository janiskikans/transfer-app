<?php

namespace App\Transaction\Repository;

use App\Transaction\Entity\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * @return Transaction[]
     */
    public function getByAccountId(string $accountId, int $offset = 0, int $limit = 100): array;

    public function save(Transaction $transaction): void;

    public function getById(string $id): ?Transaction;
}
