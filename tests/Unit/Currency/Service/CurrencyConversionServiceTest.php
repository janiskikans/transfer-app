<?php

declare(strict_types=1);

namespace App\Tests\Unit\Currency\Service;

use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Currency\Repository\CurrencyRepositoryInterface;
use App\Currency\Service\CurrencyConversionService;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\DummyFactory\Currency\CurrencyRateFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyConversionServiceTest extends TestCase
{
    private MockObject & CurrencyRateRepositoryInterface $mockedRateRepository;
    private MockObject & CurrencyRepositoryInterface $mockedCurrencyRepository;
    private CurrencyConversionService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedRateRepository = $this->createMock(CurrencyRateRepositoryInterface::class);
        $this->mockedCurrencyRepository = $this->createMock(CurrencyRepositoryInterface::class);
        $this->sut = new CurrencyConversionService($this->mockedRateRepository, $this->mockedCurrencyRepository);
    }

    public function testConvert_withSameBaseAndTargetCurrency_returnsSameAmount(): void
    {
        $this->mockedRateRepository->expects(self::never())->method('getRate');

        self::assertEquals(
            100,
            $this->sut->convert(100, CurrencyCode::USD, CurrencyCode::USD)
        );
    }

    public function testConvert_withDifferentBaseAndTargetCurrency_returnsConvertedAmount(): void
    {
        $rate = CurrencyRateFactory::create();

        $this->mockedRateRepository
            ->expects(self::once())
            ->method('getRate')
            ->with(CurrencyCode::USD, CurrencyCode::EUR, CurrencyRateSource::FAKE)
            ->willReturn($rate);

        $this->mockedCurrencyRepository
            ->method('getByCode')
            ->willReturnCallback(fn (string $currency) => match (true) {
                $currency === 'USD' => CurrencyFactory::create(),
                $currency === 'EUR' => CurrencyFactory::create('EUR', 'Euro'),
            });

        $convertedAmount = $this->sut->convert(1000, CurrencyCode::USD, CurrencyCode::EUR);

        self::assertEquals(1230, $convertedAmount);
    }

    public function testConvert_withDifferentBaseAndTargetCurrencyJPYGBP_returnsConvertedAmount(): void
    {
        $currencyJpy = CurrencyFactory::create('JPY');
        $currencyGbp = CurrencyFactory::create('GBP');
        $rate = CurrencyRateFactory::create(195.89435, baseCurrency: $currencyJpy, targetCurrency: $currencyGbp);

        $this->mockedRateRepository
            ->expects(self::once())
            ->method('getRate')
            ->with(CurrencyCode::GBP, CurrencyCode::JPY, CurrencyRateSource::FAKE)
            ->willReturn($rate);

        $this->mockedCurrencyRepository
            ->method('getByCode')
            ->willReturnCallback(fn (string $currency) => match (true) {
                $currency === 'GBP' => CurrencyFactory::create('GBP', 'British Pound Sterling'),
                $currency === 'JPY' => CurrencyFactory::create('JPY', 'Japanese Yen', 0),
            });

        $convertedAmount = $this->sut->convert(10000, CurrencyCode::GBP, CurrencyCode::JPY);

        self::assertEquals(19589, $convertedAmount);
    }

    public function testConvert_withoutRate_throwsException(): void
    {
        $this->mockedRateRepository
            ->expects(self::once())
            ->method('getRate')
            ->with(CurrencyCode::USD, CurrencyCode::EUR, CurrencyRateSource::FAKE)
            ->willReturn(null);

        self::expectExceptionObject(new CurrencyRateNotFoundException(
            'Currency rate not found for base currency: USD, target currency: EUR'
        ));

        $this->sut->convert(100, CurrencyCode::USD, CurrencyCode::EUR);
    }
}
