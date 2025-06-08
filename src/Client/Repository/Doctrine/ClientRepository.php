<?php

declare(strict_types=1);

namespace App\Client\Repository\Doctrine;

use App\Client\Entity\Client;
use App\Client\Repository\ClientRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class ClientRepository implements ClientRepositoryInterface
{
    /** @var EntityRepository<Client> */
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(Client::class);
    }

    public function getById(string $id): ?Client
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    /**
     * @return Client[]
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }
}
