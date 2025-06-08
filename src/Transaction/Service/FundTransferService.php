<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Account\Entity\Account;
use App\Currency\Enum\Currency as CurrencyEnum;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;
use App\Transaction\Exception\TransferFailedException;
use App\Transaction\Factory\TransactionFactory;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Throwable;

readonly class FundTransferService
{
    public function __construct(
        private TransferValidationService $validationService,
        private CurrencyConversionService $conversionService,
        private EntityManagerInterface $em, // TODO: Repo?,
        private TransactionFactory $transactionFactory,
        private LockFactory $lockFactory,
    ) {
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

        $lock = $this->lockFactory->createLock($this->getLockKey($sender, $recipient), 5);
        if (!$lock->acquire()) {
            throw new TransferFailedException('Fund transfer failed. Another transfer is in progress.');
        }

        $this->em->getConnection()->beginTransaction();

        try {
            $sender->debit($debitAmount);
            $this->em->persist($sender);

            $recipient->credit($creditAmount);
            $this->em->persist($recipient);

            $transaction = $this->transactionFactory->create(
                $sender,
                $recipient,
                $debitAmount,
                $transferRequest->getSender()->getCurrency()->toEnum(),
            );

            $this->em->persist($transaction);

            $this->em->flush();

            $this->em->getConnection()->commit();
        } catch (Throwable $e) {
            $this->em->getConnection()->rollBack();

            throw new TransferFailedException('Fund transfer failed', 500, previous: $e);
        } finally {
            $lock->release();
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

    private function getLockKey(Account $sender, Account $recipient): string
    {
        $ids = [$sender->getId()->toRfc4122(), $recipient->getId()->toRfc4122()];
        sort($ids);

        return 'fund_transfer_lock_' . implode('_', $ids);
    }
}
