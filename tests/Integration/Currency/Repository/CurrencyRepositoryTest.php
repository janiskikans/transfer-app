<?php

declare(strict_types=1);

namespace App\Tests\Integration\Currency\Repository;

use App\Currency\Enum\CurrencyCode;
use App\Currency\Repository\Doctrine\CurrencyRepository;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\Integration\HasEntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyRepositoryTest extends KernelTestCase
{
    use HasEntityManager;

    private CurrencyRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
        $this->sut = self::getContainer()->get(CurrencyRepository::class);
    }

    public function testGetByCode_withNonExistingCurrency_returnsNull(): void
    {
        self::assertNull($this->sut->getByCode('TST'));
    }

    public function testGetByCode_withExistingCurrency_returnsCurrency(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        $result = $this->sut->getByCode('USD');
        self::assertEquals(CurrencyCode::USD, $result->getCode());
        self::assertEquals('US Dollar', $result->getName());
        self::assertEquals(2, $result->getDecimalPlaces());
    }
}
