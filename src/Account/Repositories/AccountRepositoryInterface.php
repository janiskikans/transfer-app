<?php

namespace App\Account\Repositories;

use App\Account\Entity\Account;

interface AccountRepositoryInterface
{
    /**
     * @return Account[]
     */
    public function getByClientId(string $clientId): array;
}
