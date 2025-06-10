<?php

declare(strict_types=1);

namespace App\Tests\Integration\Currency\Service;

use App\Currency\Enum\CurrencyCode;
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

        $result = $this->sut->importAndSaveRates([CurrencyCode::USD]);
        self::assertEquals(4, $result->getNewCount());
        self::assertEquals(0, $result->getUpdatedCount());

        $insertedUsdEurRate = $this->currencyRateRepository->getRate(
            CurrencyCode::USD,
            CurrencyCode::EUR,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(0.88, $insertedUsdEurRate->getRate());
        self::assertEquals(CurrencyCode::USD, $insertedUsdEurRate->getBaseCurrency()->getCode());
        self::assertEquals(CurrencyCode::EUR, $insertedUsdEurRate->getTargetCurrency()->getCode());

        $insertedUsdGbpRate = $this->currencyRateRepository->getRate(
            CurrencyCode::USD,
            CurrencyCode::GBP,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(0.74, $insertedUsdGbpRate->getRate());
        self::assertEquals(CurrencyCode::USD, $insertedUsdGbpRate->getBaseCurrency()->getCode());
        self::assertEquals(CurrencyCode::GBP, $insertedUsdGbpRate->getTargetCurrency()->getCode());

        $insertedEurUsdRate = $this->currencyRateRepository->getRate(
            CurrencyCode::EUR,
            CurrencyCode::USD,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(1.14, $insertedEurUsdRate->getRate());
        self::assertEquals(CurrencyCode::EUR, $insertedEurUsdRate->getBaseCurrency()->getCode());
        self::assertEquals(CurrencyCode::USD, $insertedEurUsdRate->getTargetCurrency()->getCode());

        $insertedGbpUsdRate = $this->currencyRateRepository->getRate(
            CurrencyCode::GBP,
            CurrencyCode::USD,
            CurrencyRateSource::FAKE
        );
        self::assertEquals(1.35, $insertedGbpUsdRate->getRate());
        self::assertEquals(CurrencyCode::GBP, $insertedGbpUsdRate->getBaseCurrency()->getCode());
        self::assertEquals(CurrencyCode::USD, $insertedGbpUsdRate->getTargetCurrency()->getCode());
    }

    public function testImportAndSaveRates_withSomeExistingRates_updatesRates(): void
    {
        $this->prepareDb(true);

        $result = $this->sut->importAndSaveRates([CurrencyCode::USD]);

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
