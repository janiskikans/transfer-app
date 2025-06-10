<?php

declare(strict_types=1);

namespace App\Tests\Integration\Currency\Repository;

use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Repository\Doctrine\CurrencyRateRepository;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\DummyFactory\Currency\CurrencyRateFactory;
use App\Tests\Integration\HasEntityManager;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyRateRepositoryTest extends KernelTestCase
{
    use HasEntityManager;

    private CurrencyRateRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
        $this->sut = self::getContainer()->get(CurrencyRateRepository::class);
    }

    public function testGetRate_withNonExistingRate_returnsNull(): void
    {
        self::assertNull($this->sut->getRate(CurrencyCode::USD, CurrencyCode::EUR, CurrencyRateSource::FAKE));
    }

    public function testGetRate_withExistingRate_returnsRate(): void
    {
        $baseCurrency = CurrencyFactory::create();
        $targetCurrency = CurrencyFactory::create('EUR', 'Euro');

        $this->entityManager->persist($baseCurrency);
        $this->entityManager->persist($targetCurrency);

        $rate = CurrencyRateFactory::create(
            baseCurrency: $baseCurrency,
            targetCurrency: $targetCurrency,
        );

        $this->entityManager->persist($rate);
        $this->entityManager->flush();

        $result = $this->sut->getRate(CurrencyCode::USD, CurrencyCode::EUR, CurrencyRateSource::FAKE);

        self::assertEquals(1.23, $result->getRate());
        self::assertEquals($rate->getId(), $result->getId());
        self::assertEquals($rate->getBaseCurrency()->getCode(), $result->getBaseCurrency()->getCode());
        self::assertEquals($rate->getTargetCurrency()->getCode(), $result->getTargetCurrency()->getCode());
        self::assertEquals($rate->getRate(), $result->getRate());
        self::assertEquals($rate->getSource(), $result->getSource());
        self::assertInstanceOf(DateTimeImmutable::class, $result->getUpdatedAt());
    }
}
