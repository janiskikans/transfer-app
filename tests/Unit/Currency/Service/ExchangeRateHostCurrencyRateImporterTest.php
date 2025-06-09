<?php

declare(strict_types=1);

namespace App\Tests\Unit\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;
use App\Currency\Service\ExchangeRateHostCurrencyRateImporter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExchangeRateHostCurrencyRateImporterTest extends TestCase
{
    private MockHttpClient $mockedApiClient;
    private MockObject & LoggerInterface $mockedLogger;
    private ExchangeRateHostCurrencyRateImporter $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedApiClient = new MockHttpClient();
        $this->mockedLogger = $this->createMock(LoggerInterface::class);

        $this->sut = new ExchangeRateHostCurrencyRateImporter(
            $this->mockedApiClient,
            'api-key',
            $this->mockedLogger,
        );
    }

    public function testGetSource_returnsCorrectSource(): void
    {
        self::assertEquals(CurrencyRateSource::EXCHANGE_RATE_HOST, $this->sut->getSource());
    }

    public function testImportRates_withUnsuccessfulResponse_throwsException(): void
    {
        $json = <<<JSON
        {
          "success": false,
          "error": {
            "type": "Something went wrong"
          }
        }
        JSON;

        $response = new MockResponse($json);
        $this->mockedApiClient->setResponseFactory([$response]);

        self::expectExceptionObject(new CurrencyRateImporterException('Request was not successful - Something went wrong'));

        $this->sut->importRates(Currency::USD, [Currency::EUR]);
    }

    public function testImportRates_withSuccessfulResponseButNoQuotes_throwsException(): void
    {
        $json = <<<JSON
        {
          "success": true
        }
        JSON;

        $response = new MockResponse($json);
        $this->mockedApiClient->setResponseFactory([$response]);

        self::expectExceptionObject(new CurrencyRateImporterException('Response does not contain rates.'));

        $this->sut->importRates(Currency::USD, [Currency::EUR]);
    }

    public function testImportRates_withQuotesInResponseBuWithInvalidCurrency_doesNotReturnRates(): void
    {
        $json = <<<JSON
        {
          "success": true,
            "terms": "https://exchangerate.host/terms",
            "privacy": "https://exchangerate.host/privacy",
            "timestamp": 1430401802,
            "source": "USD",
            "quotes": {
              "USDFKE": 3.672982
            }
        }
        JSON;

        $response = new MockResponse($json);
        $this->mockedApiClient->setResponseFactory([$response]);

        $result = $this->sut->importRates(Currency::USD, [Currency::EUR]);
        self::assertEmpty($result);
    }

    public function testImportRates_withValidQuotesInResponse_returnsFetchedRates(): void
    {
        $json = <<<JSON
        {
          "success": true,
            "terms": "https://exchangerate.host/terms",
            "privacy": "https://exchangerate.host/privacy",
            "timestamp": 1430401802,
            "source": "USD",
            "quotes": {
              "USDEUR": 3.672982,
              "USDGBP": 0.782888
            }
        }
        JSON;

        $response = new MockResponse($json);
        $this->mockedApiClient->setResponseFactory([$response]);

        $result = $this->sut->importRates(Currency::USD, [Currency::EUR, Currency::GBP]);
        self::assertCount(2, $result);

        $rate1 = $result[0];
        self::assertEquals(Currency::USD, $rate1->baseCurrency);
        self::assertEquals(Currency::EUR, $rate1->targetCurrency);
        self::assertEquals(3.672982, $rate1->rate);

        $rate1 = $result[1];
        self::assertEquals(Currency::USD, $rate1->baseCurrency);
        self::assertEquals(Currency::GBP, $rate1->targetCurrency);
        self::assertEquals(0.782888, $rate1->rate);
    }
}
