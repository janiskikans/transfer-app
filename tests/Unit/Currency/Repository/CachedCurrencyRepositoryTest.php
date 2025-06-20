<?php

declare(strict_types=1);

namespace App\Tests\Unit\Currency\Repository;

use App\Currency\Entity\Currency;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Repository\CachedCurrencyRepository;
use App\Currency\Repository\Doctrine\CurrencyRepository;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedCurrencyRepositoryTest extends TestCase
{
    private MockObject & CacheInterface $mockedCache;
    private MockObject & CurrencyRepository $mockedRealRepository;
    private MockObject & ItemInterface $mockedItem;
    private MockObject & EntityManagerInterface $mockedEntityManager;
    private CachedCurrencyRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedRealRepository = $this->createMock(CurrencyRepository::class);
        $this->mockedCache = $this->createMock(CacheInterface::class);
        $this->mockedItem = $this->createMock(ItemInterface::class);
        $this->mockedEntityManager = $this->createMock(EntityManagerInterface::class);

        $this->sut = new CachedCurrencyRepository(
            $this->mockedRealRepository,
            $this->mockedCache,
            $this->mockedEntityManager,
        );
    }

    public function testGetByCode_withNonCachedCurrency_fetchesCurrencyFromRealRepository(): void
    {
        $this->mockedCache
            ->expects(self::once())
            ->method('get')
            ->with('currency_USD')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->mockedItem);
            });

        $currency = CurrencyFactory::create();

        $this->mockedRealRepository
            ->expects(self::once())
            ->method('getByCode')
            ->with('USD')
            ->willReturn($currency);

        $this->mockedEntityManager
            ->expects(self::once())
            ->method('getReference')
            ->with(Currency::class, CurrencyCode::USD)
            ->willReturn($currency);

        $result = $this->sut->getByCode('USD');
        self::assertEquals(CurrencyCode::USD, $result->getCode());
    }

    public function testGetByCode_withCachedCurrency_returnsRateFromCache(): void
    {
        $currency = CurrencyFactory::create();

        $this->mockedCache
            ->expects(self::once())
            ->method('get')
            ->with('currency_USD')
            ->willReturn($currency);

        $this->mockedEntityManager
            ->expects(self::once())
            ->method('getReference')
            ->with(Currency::class, CurrencyCode::USD)
            ->willReturn($currency);

        $this->mockedRealRepository->expects(self::never())->method('getByCode');

        $result = $this->sut->getByCode('USD');
        self::assertEquals(CurrencyCode::USD, $result->getCode());
    }
}
