<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Account\Entity\Account;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;
use App\Transaction\Exception\TransferFailedException;
use App\Transaction\Factory\TransactionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Throwable;

readonly class FundTransferService
{
    public function __construct(
        private TransferValidationService $validationService,
        private CurrencyConversionService $conversionService,
        private TransactionFactory $transactionFactory,
        private LockFactory $lockFactory,
        private Connection $connection,
        private string $activeRateSource,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws TransferFailedException|InvalidTransferRequestException
     * @throws Exception
     */
    public function transfer(TransferRequestDto $transferRequest): void
    {
        try {
            $this->validationService->validateTransferRequest($transferRequest);
        } catch (CurrencyRateNotFoundException $e) {
            $this->logger->error($e);

            throw new TransferFailedException('Fund transfer failed. Could not validate transfer request.');
        }

        $sender = $transferRequest->getSender();
        $recipient = $transferRequest->getRecipient();

        $debitAmount = $this->convertRequestAmountToCurrency($transferRequest, $sender->getCurrency()->getCode());

        $lock = $this->lockFactory->createLock($this->getLockKey($sender), 5);
        if (!$lock->acquire()) {
            throw new TransferFailedException('Fund transfer failed. Another transfer is in progress.');
        }

        $this->connection->beginTransaction();

        try {
            $sender->debit($debitAmount);
            $this->entityManager->persist($sender);

            $recipient->credit($transferRequest->getAmount());
            $this->entityManager->persist($recipient);

            $transaction = $this->transactionFactory->create(
                $sender,
                $recipient,
                $transferRequest->getAmount(),
                $transferRequest->getCurrency(),
            );

            $this->entityManager->persist($transaction);
            $this->entityManager->flush();

            $this->connection->commit();
        } catch (Throwable $e) {
            $this->connection->rollBack();
            $this->logger->error($e);

            throw new TransferFailedException('Fund transfer failed', 500, previous: $e);
        } finally {
            $lock->release();
        }
    }

    /**
     * @throws TransferFailedException
     */
    private function convertRequestAmountToCurrency(
        TransferRequestDto $transferRequest,
        CurrencyCode $targetCurrency
    ): int {
        try {
            return $this->conversionService->convert(
                $transferRequest->getAmount(),
                $transferRequest->getCurrency()->getCode(),
                $targetCurrency,
                CurrencyRateSource::from($this->activeRateSource),
            );
        } catch (CurrencyRateNotFoundException $e) {
            $this->logger->error($e);

            throw new TransferFailedException('Fund transfer failed. Failed to convert amount to target currency.');
        }
    }

    private function getLockKey(Account $sender): string
    {
        return 'fund_transfer_lock_' . $sender->getId()->toRfc4122();
    }
}
