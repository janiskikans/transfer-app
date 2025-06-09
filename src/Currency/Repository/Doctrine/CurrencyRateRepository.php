<?php

declare(strict_types=1);

namespace App\Currency\Repository\Doctrine;

use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

readonly class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    /** @var EntityRepository<CurrencyRate> */
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(CurrencyRate::class);
    }

    public function getRate(Currency $baseCurrency, Currency $targetCurrency, CurrencyRateSource $source): ?CurrencyRate
    {
        return $this->repository->findOneBy(
            [
                'baseCurrency' => $baseCurrency->value,
                'targetCurrency' => $targetCurrency->value,
                'source' => $source->value,
            ]
        );
    }

    public function save(CurrencyRate $rate): void
    {
        $this->entityManager->persist($rate);
        $this->entityManager->flush();
    }
}
