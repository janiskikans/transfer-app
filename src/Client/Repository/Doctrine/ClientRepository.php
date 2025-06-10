<?php

declare(strict_types=1);

namespace App\Client\Repository\Doctrine;

use App\Client\Entity\Client;
use App\Client\Repository\ClientRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);;
    }

    public function getById(string $id): ?Client
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @return Client[]
     */
    public function getAll(): array
    {
        return $this->findAll();
    }
}
