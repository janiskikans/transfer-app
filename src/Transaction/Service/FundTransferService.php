<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;

readonly class FundTransferService
{
    public function __construct(
        private TransferValidationService $validationService,
        private CurrencyConversionService $conversionService
    ) {
    }

    /**
     * @throws CurrencyRateNotFoundException
     * @throws InvalidTransferRequestException
     */
    public function transfer(TransferRequestDto $transferRequest): void
    {
        $this->validationService->validateTransferRequest($transferRequest);

        $debitAmount = $this->conversionService->convert(
            $transferRequest->getAmount(),
            $transferRequest->getCurrency(),
            $transferRequest->getSender()->getCurrency()->toEnum(),
        );

        $creditAmount = $this->conversionService->convert(
            $transferRequest->getAmount(),
            $transferRequest->getCurrency(),
            $transferRequest->getRecipient()->getCurrency()->toEnum(),
        );

        dd('debitAmount: ', $debitAmount, 'creditAmount: ', $creditAmount);
    }
}
