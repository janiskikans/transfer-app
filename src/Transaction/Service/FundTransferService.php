<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Currency\Enum\Currency as CurrencyEnum;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;
use App\Transaction\Exception\TransferFailedException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

readonly class FundTransferService
{
    public function __construct(
        private TransferValidationService $validationService,
        private CurrencyConversionService $conversionService,
        private EntityManagerInterface $em, // TODO: Repo?
    )
    {
    }

    /**
     * @throws TransferFailedException|InvalidTransferRequestException|CurrencyRateNotFoundException|Exception
     */
    public function transfer(TransferRequestDto $transferRequest): void
    {
        $this->validationService->validateTransferRequest($transferRequest);

        $sender = $transferRequest->getSender();
        $recipient = $transferRequest->getRecipient();

        $debitAmount = $this->convertRequestAmountToCurrency($transferRequest, $sender->getCurrency()->toEnum());
        $creditAmount = $this->convertRequestAmountToCurrency($transferRequest, $recipient->getCurrency()->toEnum());

        // TODO: Mutex
        // TODO: Transaction entity

        $this->em->getConnection()->beginTransaction();

        try {
            $sender->debit($debitAmount);
            $this->em->persist($sender);

            $recipient->credit($creditAmount);
            $this->em->persist($recipient);

            $this->em->flush();

            $this->em->getConnection()->commit();
        } catch (Throwable $e) {
            $this->em->getConnection()->rollBack();

            throw new TransferFailedException('Fund transfer failed', 500, previous: $e);
        }
    }

    /**
     * @throws CurrencyRateNotFoundException
     */
    private function convertRequestAmountToCurrency(
        TransferRequestDto $transferRequest,
        CurrencyEnum $targetCurrency
    ): int {
        return $this->conversionService->convert(
            $transferRequest->getAmount(),
            $transferRequest->getCurrency(),
            $targetCurrency,
        );
    }
}
