<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Dto\CurrencyRateImportData;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;
use App\Currency\Interface\CurrencyRateImporterInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class ExchangeRateHostCurrencyRateImporter implements CurrencyRateImporterInterface
{
    private const string BASE_URL = 'https://api.exchangerate.host';

    public function __construct(
        private HttpClientInterface $client,
        private string $exchangeRateHostAccessKey,
        private LoggerInterface $logger
    ) {
    }

    public function getSource(): CurrencyRateSource
    {
        return CurrencyRateSource::EXCHANGE_RATE_HOST;
    }

    /**
     * @param Currency[]|null $targetCurrencies
     * @return CurrencyRateImportData[]
     * @throws CurrencyRateImporterException
     */
    public function importRates(Currency $sourceCurrency, ?array $targetCurrencies = null): array
    {
        try {
            $query = [
                'access_key' => $this->exchangeRateHostAccessKey,
                'source' => $sourceCurrency->value,
                'date' => new DateTimeImmutable()->format('Y-m-d'),
            ];

            if ($targetCurrencies) {
                $currencies = array_map(fn(Currency $currency) => $currency->value, $targetCurrencies);
                $query['currencies'] = implode(',', $currencies);
            }

            $response = $this->client->request('GET', self::BASE_URL . '/historical', [
                'query' => $query
            ]);

            $data = $response->toArray();

            if (!isset($data['success']) || $data['success'] === false) {
                $apiError = $data['error']['type'] ?? 'Unknown error';

                throw new CurrencyRateImporterException('Request was not successful - ' . $apiError);
            }

            if (!isset($data['quotes'])) {
                throw new CurrencyRateImporterException('Response does not contain rates.');
            }

            $rates = [];

            foreach ($data['quotes'] as $key => $rate) {
                $baseCurrency = Currency::tryFrom(substr($key, 0, 3));
                $targetCurrency = Currency::tryFrom(substr($key, 3, 3));

                if (!$baseCurrency || !$targetCurrency) {
                    continue;
                }

                $rates[] = new CurrencyRateImportData($baseCurrency, $targetCurrency, $rate);
            }

            return $rates;
        } catch (CurrencyRateImporterException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logger->error($e);

            throw new CurrencyRateImporterException('Failed to import currency rates from ExchangeRateHost.', 0, $e);
        }
    }
}
