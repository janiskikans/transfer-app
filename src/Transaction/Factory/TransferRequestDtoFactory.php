<?php

declare(strict_types=1);

namespace App\Transaction\Factory;

use App\Account\Repository\AccountRepositoryInterface;
use App\Currency\Repository\CurrencyRepositoryInterface;
use App\Shared\Helper\MoneyAmountHelper;
use App\Transaction\Dto\TransferPostRequestDto;
use App\Transaction\Dto\TransferRequestDto;
use RuntimeException;

final readonly class TransferRequestDtoFactory
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private CurrencyRepositoryInterface $currencyRepository
    ) {
    }

    public function fromTransferPostRequest(TransferPostRequestDto $requestDto): TransferRequestDto
    {
        $senderAccount = $this->accountRepository->getById($requestDto->senderAccountId);
        if (!$senderAccount) {
            throw new RuntimeException('Sender account not found');
        }

        $recipientAccount = $this->accountRepository->getById($requestDto->recipientAccountId);
        if (!$recipientAccount) {
            throw new RuntimeException('Recipient account not found');
        }

        $currency = $this->currencyRepository->getByCode($requestDto->currency);
        if (!$currency) {
            throw new RuntimeException('Invalid currency given');
        }

        return new TransferRequestDto(
            $senderAccount,
            $recipientAccount,
            MoneyAmountHelper::convertToMinor($requestDto->amount, $currency->getDecimalPlaces()),
            $currency,
        );
    }
}
