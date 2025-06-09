<?php

declare(strict_types=1);

namespace App\Tests\Integration\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Repository\Doctrine\CurrencyRateRepository;
use App\Currency\Service\CurrencyRateImportService;
use App\Currency\Service\FakeCurrencyRateImporter;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\DummyFactory\Currency\CurrencyRateFactory;
use App\Tests\Integration\HasEntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyRateImportServiceTest extends KernelTestCase
{
    use HasEntityManager;

    private CurrencyRateRepository $currencyRateRepository;
    private CurrencyRateImportService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();

        $this->currencyRateRepository = self::getContainer()->get(CurrencyRateRepository::class);

        $this->sut = new CurrencyRateImportService(
            self::getContainer()->get(FakeCurrencyRateImporter::class),
            self::getContainer()->get(CurrencyRateRepository::class),
            $this->entityManager,
        );
    }

    public function testImportAndSaveRates_withNoExistingRates_savesNewRates(): void
    {
        $this->prepareDb();

        $result = $this->sut->importAndSaveRates([Currency::USD]);
        self::assertEquals(4, $result->getNewCount());
        self::assertEquals(0, $result->getUpdatedCount());

        $insertedUsdEurRate = $this->currencyRateRepository->getRate(
            Currency::USD,
            Currency::EUR,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(0.88, $insertedUsdEurRate->getRate());
        self::assertEquals(Currency::USD, $insertedUsdEurRate->getBaseCurrency()->toEnum());
        self::assertEquals(Currency::EUR, $insertedUsdEurRate->getTargetCurrency()->toEnum());

        $insertedUsdGbpRate = $this->currencyRateRepository->getRate(
            Currency::USD,
            Currency::GBP,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(0.74, $insertedUsdGbpRate->getRate());
        self::assertEquals(Currency::USD, $insertedUsdGbpRate->getBaseCurrency()->toEnum());
        self::assertEquals(Currency::GBP, $insertedUsdGbpRate->getTargetCurrency()->toEnum());

        $insertedEurUsdRate = $this->currencyRateRepository->getRate(
            Currency::EUR,
            Currency::USD,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(1.14, $insertedEurUsdRate->getRate());
        self::assertEquals(Currency::EUR, $insertedEurUsdRate->getBaseCurrency()->toEnum());
        self::assertEquals(Currency::USD, $insertedEurUsdRate->getTargetCurrency()->toEnum());

        $insertedGbpUsdRate = $this->currencyRateRepository->getRate(
            Currency::GBP,
            Currency::USD,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(1.35, $insertedGbpUsdRate->getRate());
        self::assertEquals(Currency::GBP, $insertedGbpUsdRate->getBaseCurrency()->toEnum());
        self::assertEquals(Currency::USD, $insertedGbpUsdRate->getTargetCurrency()->toEnum());
    }

    public function testImportAndSaveRates_withSomeExistingRates_updatesRates(): void
    {
        $this->prepareDb(true);

        $result = $this->sut->importAndSaveRates([Currency::USD]);

        self::assertEquals(2, $result->getNewCount());
        self::assertEquals(2, $result->getUpdatedCount());
    }

    private function prepareDb(bool $withExistingRates = false): void
    {
        $usdCurrency = CurrencyFactory::create();
        $this->entityManager->persist($usdCurrency);

        $eurCurrency = CurrencyFactory::create('EUR', 'Euro');
        $this->entityManager->persist($eurCurrency);

        $gbpCurrency = CurrencyFactory::create('GBP', 'British Pound');
        $this->entityManager->persist($gbpCurrency);

        if ($withExistingRates) {
            $usdEurRate = CurrencyRateFactory::create(0.88, $usdCurrency, $eurCurrency);
            $this->entityManager->persist($usdEurRate);

            $eurUsdRate = CurrencyRateFactory::create(1.14, $eurCurrency, $usdCurrency);
            $this->entityManager->persist($eurUsdRate);
        }

        $this->entityManager->flush();
    }
}
