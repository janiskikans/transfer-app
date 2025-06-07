<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;
use App\Currency\Interface\CurrencyRateImporterInterface;
use App\Currency\Structure\CurrencyRateImportData;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class ExchangeRateHostCurrencyRateImporter implements CurrencyRateImporterInterface
{
    private const string BASE_URL = 'https://api.exchangerate.host';

    public function __construct(private HttpClientInterface $client, private string $exchangeRateHostAccessKey)
    {
    }

    public function getSource(): CurrencyRateSource
    {
        return CurrencyRateSource::EXCHANGE_RATE_HOST;
    }

    /**
     * @param Currency[] $currencies $currencies
     * @return CurrencyRateImportData[]
     */
    public function importRates(Currency $source, array $currencies): array
    {
        if (!$currencies) {
            return [];
        }

        try {
            $currencies = array_map(fn(Currency $currency) => $currency->value, $currencies);

            $response = $this->client->request('GET', self::BASE_URL . '/historical', [
                'query' => [
                    'access_key' => $this->exchangeRateHostAccessKey,
                    'source' => $source->value,
                    'currencies' => implode(',', $currencies),
                    'date' => new DateTimeImmutable()->format('Y-m-d'),
                ]
            ]);

            $data = $response->toArray();

            dump($data);

            if (!isset($data['success']) || $data['success'] === false) {
                throw new CurrencyRateImporterException('Request was not successful.');
            }

            if (!isset($data['quotes'])) {
                throw new CurrencyRateImporterException('Response does not contain rates.');
            }

            $rates = [];

            foreach ($data['quotes'] as $key => $rate) {
                $rates[] = new CurrencyRateImportData(
                    substr($key, 3),
                    substr($key, 3, 3),
                    $rate,
                );
            }

            return $rates;
        } catch (CurrencyRateImporterException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CurrencyRateImporterException('Failed to import currency rates from ExchangeRateHost.', 0, $e);
        }
    }
}
