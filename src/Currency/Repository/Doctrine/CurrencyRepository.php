<?php

declare(strict_types=1);

namespace App\Currency\Repository\Doctrine;

use App\Currency\Entity\Currency;
use App\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyRepository extends ServiceEntityRepository implements CurrencyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);;
    }

    public function getByCode(string $code): ?Currency
    {
        return $this->findOneBy(['code' => $code]);
    }
}
