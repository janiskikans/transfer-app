<?php

declare(strict_types=1);

namespace App\Currency\Repository;

use App\Currency\Entity\Currency;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CachedCurrencyRepository implements CurrencyRepositoryInterface
{
    public function __construct(private CurrencyRepositoryInterface $innerRepository, private CacheInterface $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByCode(string $code): ?Currency
    {
        return $this->cache->get('currency_' . $code, function (ItemInterface $item) use ($code): ?Currency {
            $item->expiresAfter(12 * 60 * 60); // 12 hours

            return $this->innerRepository->getByCode($code);
        });
    }
}
