<?php

namespace App\Client\Repositories;

use App\Client\Entity\Client;

interface ClientRepositoryInterface
{
    public function getById(string $id): ?Client;
}
