<?php

namespace App\Account\Repository;

use App\Account\Entity\Account;

interface AccountRepositoryInterface
{
    /**
     * @return Account[]
     */
    public function getByClientId(string $clientId): array;

    public function getById(string $id): ?Account;

    public function save(Account $account): void;
}
