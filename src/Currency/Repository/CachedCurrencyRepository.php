<?php

declare(strict_types=1);

namespace App\Currency\Repository;

use App\Currency\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CachedCurrencyRepository implements CurrencyRepositoryInterface
{
    public function __construct(
        private CurrencyRepositoryInterface $innerRepository,
        private CacheInterface $cache,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByCode(string $code): ?Currency
    {
        $currency = $this->cache->get('currency_' . $code, function (ItemInterface $item) use ($code): ?Currency {
            $item->expiresAfter(12 * 60 * 60); // 12 hours

            return $this->innerRepository->getByCode($code);
        });

        return $this->entityManager->getReference(Currency::class, $currency->getCode());
    }
}
