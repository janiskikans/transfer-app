<?php

declare(strict_types=1);

namespace App\Currency\Repository;

use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\Currency;
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
    public function getRate(Currency $baseCurrency, Currency $targetCurrency, CurrencyRateSource $source): ?CurrencyRate
    {
        return $this->cache->get(
            $this->getCacheKey($baseCurrency, $targetCurrency, $source),
            function (ItemInterface $item) use ($baseCurrency, $targetCurrency, $source) {
                $item->expiresAfter(600);

                return $this->innerRepository->getRate($baseCurrency, $targetCurrency, $source);
            }
        );
    }

    public function save(CurrencyRate $rate): void
    {
        $this->innerRepository->save($rate);
    }

    private function getCacheKey(Currency $baseCurrency, Currency $targetCurrency, CurrencyRateSource $source): string
    {
        return sprintf('%s-%s-%s', $baseCurrency->value, $targetCurrency->value, $source->value);
    }
}
