<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Dto\CurrencyRateImportData;
use App\Currency\Dto\CurrencyRateImportResult;
use App\Currency\Entity\Currency as CurrencyEntity;
use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\Currency;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateImporterException;
use App\Currency\Interface\CurrencyRateImporterInterface;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

readonly class CurrencyRateImportService
{
    private const array CURRENCIES = [
        Currency::USD,
        Currency::EUR,
        Currency::GBP,
        Currency::JPY,
        Currency::ISK,
    ];

    private const array SOURCE_RATE_LIMITER_DELAY = [
        // api.exchangerate.host has a crazy rate limit :/
        CurrencyRateSource::EXCHANGE_RATE_HOST->value => 1,
    ];

    public function __construct(
        private CurrencyRateImporterInterface $rateImporter,
        private CurrencyRateRepositoryInterface $rateRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws ORMException
     * @throws CurrencyRateImporterException
     */
    public function importAndSaveRates(array $currencies = self::CURRENCIES): CurrencyRateImportResult
    {
        $result = new CurrencyRateImportResult();

        foreach ($currencies as $currency) {
            $rates = $this->rateImporter->importRates($currency);
            if (!$rates) {
                continue;
            }

            $this->saveRates($rates, $result);
            $this->waitForNextImportIfNecessary($this->rateImporter->getSource());
        }

        return $result;
    }

    /**
     * @param CurrencyRateImportData[] $fetchedRates
     * @throws ORMException
     */
    private function saveRates(array $fetchedRates, CurrencyRateImportResult $result): void
    {
        $source = $this->rateImporter->getSource();

        foreach ($fetchedRates as $rateData) {
            $rate = $this->rateRepository->getRate(
                $rateData->baseCurrency,
                $rateData->targetCurrency,
                $this->rateImporter->getSource()
            );

            if ($rate) {
                $rate->setRate($rateData->rate);
                $this->entityManager->persist($rate);
                $result->onUpdatedRate();

                continue;
            }

            $rate = new CurrencyRate(
                source: $source,
                baseCurrency: $this->entityManager->getReference(
                    CurrencyEntity::class,
                    $rateData->baseCurrency->value,
                ),
                targetCurrency: $this->entityManager->getReference(
                    CurrencyEntity::class,
                    $rateData->targetCurrency->value,
                ),
                rate: $rateData->rate,
            );

            $this->entityManager->persist($rate);
            $result->onNewRate();
        }

        $this->entityManager->flush();
    }

    private function waitForNextImportIfNecessary(CurrencyRateSource $source): void
    {
        $delayInSeconds = self::SOURCE_RATE_LIMITER_DELAY[$source->value] ?? null;
        if (is_null($delayInSeconds)) {
            return;
        }

        sleep($delayInSeconds);
    }
}
