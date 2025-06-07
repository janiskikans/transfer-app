<?php

namespace App\Client\Repository;

use App\Client\Entity\Client;

interface ClientRepositoryInterface
{
    public function getById(string $id): ?Client;
}
