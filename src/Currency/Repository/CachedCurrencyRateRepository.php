<?php

declare(strict_types=1);

namespace App\Currency\Repository;

use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CachedCurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    public function __construct(private CurrencyRateRepositoryInterface $innerRepository, private CacheInterface $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getRate(CurrencyCode $baseCurrency, CurrencyCode $targetCurrency, CurrencyRateSource $source): ?CurrencyRate
    {
        return $this->cache->get(
            $this->getCacheKey($baseCurrency, $targetCurrency, $source),
            function (ItemInterface $item) use ($baseCurrency, $targetCurrency, $source): ?CurrencyRate {
                $item->expiresAfter(600); // 10 minutes

                return $this->innerRepository->getRate($baseCurrency, $targetCurrency, $source);
            }
        );
    }

    private function getCacheKey(CurrencyCode $baseCurrency, CurrencyCode $targetCurrency, CurrencyRateSource $source): string
    {
        return sprintf('%s-%s-%s', $baseCurrency->value, $targetCurrency->value, $source->value);
    }
}
