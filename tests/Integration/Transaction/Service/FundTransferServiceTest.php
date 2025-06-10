<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction\Service;

use App\Account\Entity\Account;
use App\Client\Entity\Client;
use App\Currency\Entity\Currency;
use App\Currency\Enum\CurrencyCode;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Client\ClientFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\DummyFactory\Currency\CurrencyRateFactory;
use App\Tests\Integration\HasEntityManager;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\TransferFailedException;
use App\Transaction\Repository\TransactionRepositoryInterface;
use App\Transaction\Service\FundTransferService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\SharedLockInterface;

class FundTransferServiceTest extends KernelTestCase
{
    use HasEntityManager;

    private TransactionRepositoryInterface $transactionRepository;
    private FundTransferService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();

        $this->transactionRepository = self::getContainer()->get(TransactionRepositoryInterface::class);

        $this->sut = self::getContainer()->get(FundTransferService::class);
    }

    public function testTransfer_withMissingCurrencyRate_throwsException(): void
    {
        $client = $this->createClient();

        $usdCurrency = $this->createCurrency();
        $eurCurrency = $this->createCurrency(CurrencyCode::EUR);

        $senderAccount = $this->createAccount($client, $usdCurrency);
        $recipientAccount = $this->createAccount($client, $eurCurrency);

        $request = $this->buildRequest(
            senderAccount: $senderAccount,
            recipientAccount: $recipientAccount,
            amount: 1000,
            currency: $eurCurrency,
        );

        self::expectExceptionObject(
            new TransferFailedException('Fund transfer failed. Could not validate transfer request.')
        );

        $this->sut->transfer($request);
    }

    public function testTransfer_withExistingLock_throwsException(): void
    {
        $client = $this->createClient();

        $usdCurrency = $this->createCurrency();

        $senderAccount = $this->createAccount($client, $usdCurrency);
        $recipientAccount = $this->createAccount($client, $usdCurrency);

        $request = $this->buildRequest(
            senderAccount: $senderAccount,
            recipientAccount: $recipientAccount,
            amount: 1000,
            currency: $usdCurrency,
        );

        /** @var SharedLockInterface $lock */
        $lock = self::getContainer()->get(LockFactory::class)->createLock(
            'fund_transfer_lock_' . $senderAccount->getId()->toRfc4122(),
            5
        );

        $lock->acquire();

        self::expectExceptionObject(
            new TransferFailedException('Fund transfer failed. Another transfer is in progress.')
        );

        $this->sut->transfer($request);

        $lock->release();
    }

    public function testTransfer_withSuccessfulTransfer_adjustsAccountBalancesAndCreatesTransaction(): void
    {
        $client = $this->createClient();

        $usdCurrency = $this->createCurrency();
        $eurCurrency = $this->createCurrency(CurrencyCode::EUR);

        $senderAccount = $this->createAccount($client, $usdCurrency);
        $recipientAccount = $this->createAccount($client, $eurCurrency);

        $eurUsdRate = CurrencyRateFactory::create(
            rate: 1.30,
            baseCurrency: $eurCurrency,
            targetCurrency: $usdCurrency,
        );

        $this->entityManager->persist($eurUsdRate);
        $this->entityManager->flush();

        $request = $this->buildRequest(
            senderAccount: $senderAccount,
            recipientAccount: $recipientAccount,
            amount: 1000,
            currency: $eurCurrency,
        );

        $this->sut->transfer($request);

        $transaction = $this->transactionRepository->getByAccountId($senderAccount->getId()->toString())[0];
        self::assertEquals(1000, $transaction->getAmount());
        self::assertEquals($senderAccount->getId(), $transaction->getSender()->getId());
        self::assertEquals($recipientAccount->getId(), $transaction->getRecipient()->getId());
        self::assertEquals($eurCurrency->getCode(), $transaction->getCurrency()->getCode());

        self::assertEquals(11_000, $recipientAccount->getBalance());
        self::assertEquals(8700, $senderAccount->getBalance());
    }

    private function buildRequest(
        Account $senderAccount,
        Account $recipientAccount,
        int $amount,
        Currency $currency,
    ): TransferRequestDto {
        return new TransferRequestDto(
            $senderAccount,
            $recipientAccount,
            $amount,
            $currency,
        );
    }

    private function createAccount(Client $client, Currency $currency, int $balance = 10_000): Account
    {
        $account = AccountFactory::create(
            balance: $balance,
            client: $client,
            currency: $currency,
        );

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }

    private function createCurrency(CurrencyCode $currencyCode = CurrencyCode::USD): Currency
    {
        $currency = CurrencyFactory::create($currencyCode->value);

        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }

    private function createClient(): Client
    {
        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $client;
    }
}
