<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction\Repository\Doctrine;

use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Client\ClientFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\DummyFactory\Transaction\TransactionFactory;
use App\Tests\Integration\HasEntityManager;
use App\Transaction\Repository\Doctrine\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class TransactionRepositoryTest extends KernelTestCase
{
    use HasEntityManager;

    private TransactionRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
        $this->sut = self::getContainer()->get(TransactionRepository::class);
    }

    public function testGetByAccountId_withoutExistingTransactions_returnsEmptyArray(): void
    {
        self::assertEmpty($this->sut->getByAccountId(Uuid::v6()->toString()));
    }

    public function testGetByAccountId_withDebitAndCreditTransactions_returnsCorrectTransactions(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $client = ClientFactory::create();
        $this->entityManager->persist($client);

        $account = AccountFactory::create(client: $client, currency: $currency);
        $this->entityManager->persist($account);

        $otherAccount = AccountFactory::create(client: $client, currency: $currency);
        $this->entityManager->persist($otherAccount);

        $debitTransaction = TransactionFactory::create(
            senderAccount: $account,
            recipientAccount: $otherAccount,
            currency: $currency
        );
        $this->entityManager->persist($debitTransaction);

        $creditTransaction = TransactionFactory::create(
            senderAccount: $otherAccount,
            recipientAccount: $account,
            currency: $currency
        );
        $this->entityManager->persist($creditTransaction);
        $this->entityManager->flush();

        $result = $this->sut->getByAccountId($account->getId()->toString());
        self::assertCount(2, $result);
        self::assertEquals($debitTransaction->getId(), $result[0]->getId());
        self::assertEquals($creditTransaction->getId(), $result[1]->getId());
    }

    public function testSave_withTransaction_transactionIsSaved(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $client = ClientFactory::create();
        $this->entityManager->persist($client);

        $account = AccountFactory::create(client: $client, currency: $currency);
        $this->entityManager->persist($account);

        $transaction = TransactionFactory::create(
            senderAccount: $account,
            recipientAccount: $account,
            currency: $currency,
        );

        $this->sut->save($transaction);

        $savedTransaction = $this->sut->getById($transaction->getId()->toString());
        self::assertNotNull($savedTransaction);
        self::assertEquals($transaction->getId(), $savedTransaction->getId());
        self::assertEquals($transaction->getAmount(), $savedTransaction->getAmount());
        self::assertEquals($transaction->getCurrency()->toEnum(), $savedTransaction->getCurrency()->toEnum());
        self::assertEquals($transaction->getSender()->getId(), $savedTransaction->getSender()->getId());
        self::assertEquals($transaction->getRecipient()->getId(), $savedTransaction->getRecipient()->getId());
        self::assertInstanceOf(\DateTimeImmutable::class, $savedTransaction->getCreatedAt());
    }
}
