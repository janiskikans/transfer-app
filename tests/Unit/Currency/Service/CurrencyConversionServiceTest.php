<?php

declare(strict_types=1);

namespace App\Tests\Unit\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Currency\Service\CurrencyConversionService;
use App\Tests\DummyFactory\Currency\CurrencyRateFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyConversionServiceTest extends TestCase
{
    private MockObject & CurrencyRateRepositoryInterface $mockedRateRepository;
    private CurrencyConversionService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedRateRepository = $this->createMock(CurrencyRateRepositoryInterface::class);
        $this->sut = new CurrencyConversionService($this->mockedRateRepository);
    }

    public function testConvert_withSameBaseAndTargetCurrency_returnsSameAmount(): void
    {
        $this->mockedRateRepository->expects(self::never())->method('getRate');

        self::assertEquals(
            100,
            $this->sut->convert(100, Currency::USD, Currency::USD)
        );
    }

    public function testConvert_withDifferentBaseAndTargetCurrency_returnsConvertedAmount(): void
    {
        $rate = CurrencyRateFactory::create();

        $this->mockedRateRepository
            ->expects(self::once())
            ->method('getRate')
            ->with(Currency::USD, Currency::EUR, CurrencyRateSource::FAKE)
            ->willReturn($rate);

        $convertedAmount = $this->sut->convert(100, Currency::USD, Currency::EUR);

        self::assertEquals(123, $convertedAmount);
    }

    public function testConvert_withoutRate_throwsException(): void
    {
        $this->mockedRateRepository
            ->expects(self::once())
            ->method('getRate')
            ->with(Currency::USD, Currency::EUR, CurrencyRateSource::FAKE)
            ->willReturn(null);

        self::expectExceptionObject(new CurrencyRateNotFoundException(
            'Currency rate not found for base currency: USD, target currency: EUR'
        ));

        $this->sut->convert(100, Currency::USD, Currency::EUR);
    }
}
