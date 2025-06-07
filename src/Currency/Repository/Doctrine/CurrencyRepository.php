<?php

declare(strict_types=1);

namespace App\Currency\Repository\Doctrine;

use App\Currency\Entity\Currency;
use App\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class CurrencyRepository implements CurrencyRepositoryInterface
{
    /** @var EntityRepository<Currency> */
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(Currency::class);
    }

    public function getByCode(string $code): ?Currency
    {
        return $this->repository->findOneBy(['code' => $code]);
    }
}
