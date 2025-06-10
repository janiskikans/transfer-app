<?php

declare(strict_types=1);

namespace App\Currency\Repository\Doctrine;

use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyRateRepository extends ServiceEntityRepository implements CurrencyRateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    public function getRate(Currency $baseCurrency, Currency $targetCurrency, CurrencyRateSource $source): ?CurrencyRate
    {
        return $this->findOneBy(
            [
                'baseCurrency' => $baseCurrency->value,
                'targetCurrency' => $targetCurrency->value,
                'source' => $source->value,
            ]
        );
    }
}
