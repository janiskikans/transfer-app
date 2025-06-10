<?php

declare(strict_types=1);

namespace App\Tests\Unit\Transaction\Factory;

use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Transaction\TransactionFactory;
use App\Transaction\Enum\TransactionType;
use App\Transaction\Factory\TransactionDtoFactory;
use PHPUnit\Framework\TestCase;

class TransactionDtoFactoryTest extends TestCase
{
    private TransactionDtoFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new TransactionDtoFactory();
    }

    public function testCreateFromEntity_withDebitTransaction_returnsCorrectDto(): void
    {
        $debitAccount = AccountFactory::create();

        $transaction = TransactionFactory::create(
            senderAccount: $debitAccount,
        );

        $dto = $this->sut->createFromEntity($transaction, $debitAccount);
        self::assertEquals(TransactionType::DEBIT, $dto->type);
        self::assertEquals($transaction->getId(), $dto->id);
        self::assertEquals($transaction->getSender()->getId(), $dto->sender);
        self::assertEquals($transaction->getRecipient()->getId(), $dto->recipient);
        self::assertEquals(10.00, $dto->amount);
        self::assertEquals($transaction->getCurrency()->getCode()->value, $dto->currency);
        self::assertEquals($transaction->getCreatedAt(), $dto->createdAt);
    }

    public function testCreateFromEntity_withCreditTransaction_returnsCorrectDto(): void
    {
        $creditAccount = AccountFactory::create();

        $transaction = TransactionFactory::create(
            recipientAccount: $creditAccount,
        );

        $dto = $this->sut->createFromEntity($transaction, $creditAccount);
        self::assertEquals(TransactionType::CREDIT, $dto->type);
    }
}
