<?php

declare(strict_types=1);

namespace App\Tests\Unit\Currency\Repository;

use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Repository\CachedCurrencyRateRepository;
use App\Currency\Repository\Doctrine\CurrencyRateRepository;
use App\Tests\DummyFactory\Currency\CurrencyRateFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedCurrencyRateRepositoryTest extends TestCase
{
    private MockObject & CacheInterface $mockedCache;
    private MockObject & CurrencyRateRepository $mockedRealRepository;
    private MockObject & ItemInterface $mockedItem;
    private CachedCurrencyRateRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedRealRepository = $this->createMock(CurrencyRateRepository::class);
        $this->mockedCache = $this->createMock(CacheInterface::class);
        $this->mockedItem = $this->createMock(ItemInterface::class);

        $this->sut = new CachedCurrencyRateRepository(
            $this->mockedRealRepository,
            $this->mockedCache,
        );
    }

    public function testGetRate_withNonCachedCurrencyRate_fetchesRateFromRealRepository(): void
    {
        $this->mockedCache
            ->expects(self::once())
            ->method('get')
            ->with('USD-EUR-FKE')
            ->willReturnCallback(function ($key, $callback){
                return $callback($this->mockedItem);
            });

        $rate = CurrencyRateFactory::create();

        $this->mockedRealRepository
            ->expects(self::once())
            ->method('getRate')
            ->with(
                CurrencyCode::USD,
                CurrencyCode::EUR,
                CurrencyRateSource::FAKE,
            )
            ->willReturn($rate);

        $result = $this->sut->getRate(
            CurrencyCode::USD,
            CurrencyCode::EUR,
            CurrencyRateSource::FAKE,
        );

        self::assertEquals(1.23, $result->getRate());
    }

    public function testGetRate_withCachedCurrencyRate_returnsRateFromCache(): void
    {
        $rate = CurrencyRateFactory::create();

        $this->mockedCache
            ->expects(self::once())
            ->method('get')
            ->with('USD-EUR-FKE')
            ->willReturn($rate);

        $this->mockedRealRepository->expects(self::never())->method('getRate');

        $result = $this->sut->getRate(
            CurrencyCode::USD,
            CurrencyCode::EUR,
            CurrencyRateSource::FAKE,
        );

        self::assertEquals(1.23, $result->getRate());
    }
}
