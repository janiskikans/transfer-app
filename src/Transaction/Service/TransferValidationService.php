<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;

readonly class TransferValidationService
{
    public function __construct(private CurrencyConversionService $conversionService, private string $activeRateSource)
    {
    }

    /**
     * @throws InvalidTransferRequestException
     * @throws CurrencyRateNotFoundException
     */
    public function validateTransferRequest(TransferRequestDto $request): void
    {
        if ($request->getAmount() <= 0) {
            // TODO: Maybe custom exceptions for each
            throw new InvalidTransferRequestException('Transfer amount must be greater than zero.');
        }

        if ($request->getSender()->getId() === $request->getRecipient()->getId()) {
            throw new InvalidTransferRequestException('Sender and recipient cannot be the same.');
        }

        $isValidRequestCurrency = $request->getCurrency() === $request->getSender()->getCurrency()->toEnum()
            || $request->getCurrency() === $request->getRecipient()->getCurrency()->toEnum();

        if (!$isValidRequestCurrency) {
            throw new InvalidTransferRequestException('Invalid currency.');
        }

        $this->validateSenderBalance($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    private function validateSenderBalance(TransferRequestDto $request): void
    {
        $debitAmount = $this->conversionService->convert(
            $request->getAmount(),
            $request->getCurrency(),
            $request->getSender()->getCurrency()->toEnum(),
            CurrencyRateSource::from($this->activeRateSource),
        );

        if ($debitAmount > $request->getSender()->getBalance()) {
            throw new InvalidTransferRequestException('Sender has insufficient funds.');
        }
    }
}
