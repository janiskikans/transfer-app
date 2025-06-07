<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;

readonly class FundTransferService
{
    public function __construct(private TransferValidationService $validationService)
    {
    }

    /**
     * @throws CurrencyRateNotFoundException
     * @throws InvalidTransferRequestException
     */
    public function transfer(TransferRequestDto $transferRequest): void
    {
        $this->validationService->validateTransferRequest($transferRequest);
    }
}
