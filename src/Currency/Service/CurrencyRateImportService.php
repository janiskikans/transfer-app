<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Entity\Currency as CurrencyEntity;
use App\Currency\Entity\CurrencyRate;
use App\Currency\Enum\Currency;
use App\Currency\Interface\CurrencyRateImporterInterface;
use App\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Currency\Structure\CurrencyRateImportData;
use App\Currency\Structure\CurrencyRateImportResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

readonly class CurrencyRateImportService
{
    private const array CURRENCIES = [
        Currency::USD,
        Currency::EUR,
        Currency::GBP,
        Currency::JPY,
        Currency::AUD,
        Currency::CAD,
    ];

    public function __construct(
        private CurrencyRateImporterInterface $rateImporter,
        private CurrencyRateRepositoryInterface $rateRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws ORMException
     */
    public function importAndSaveRates(): CurrencyRateImportResult
    {
        $result = new CurrencyRateImportResult();

        foreach (self::CURRENCIES as $currency) {
            $rates = $this->rateImporter->importRates($currency);
            if (!$rates) {
                continue;
            }

            $this->saveRates($rates, $result);
        }

        return $result;
    }

    /**
     * @param CurrencyRateImportData[] $fetchedRates
     * @throws ORMException
     */
    private function saveRates(array $fetchedRates, CurrencyRateImportResult &$result): void
    {
        $source = $this->rateImporter->getSource();

        foreach ($fetchedRates as $rateData) {
            $rate = $this->rateRepository->getRate(
                $rateData->getBaseCurrency(),
                $rateData->getTargetCurrency(),
                $this->rateImporter->getSource()
            );

            if ($rate) {
                $rate->setRate($rateData->getRate());
                $this->rateRepository->save($rate);
                $result->onUpdatedRate();

                continue;
            }

            $rate = new CurrencyRate(
                source: $source,
                baseCurrency: $this->entityManager->getReference(
                    CurrencyEntity::class,
                    $rateData->getBaseCurrency()->value,
                ),
                targetCurrency: $this->entityManager->getReference(
                    CurrencyEntity::class,
                    $rateData->getTargetCurrency()->value,
                ),
                rate: $rateData->getRate(),
            );

            $this->rateRepository->save($rate);
            $result->onNewRate();
        }
    }
}
