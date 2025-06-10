<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction\Factory;

use App\Currency\Enum\CurrencyCode;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\Integration\HasEntityManager;
use App\Transaction\Factory\TransactionFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionFactoryTest extends KernelTestCase
{
    use HasEntityManager;

    private TransactionFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
        $this->sut = new TransactionFactory();
    }

    public function testCreate_returnsCorrectTransaction(): void
    {
        $eurCurrency = CurrencyFactory::create(CurrencyCode::EUR->value);

        $sender = AccountFactory::create();
        $recipient = AccountFactory::create();

        $result = $this->sut->create(
            sender: $sender,
            recipient: $recipient,
            amount: 1000,
            currency: $eurCurrency,
        );

        self::assertEquals(1000, $result->getAmount());
        self::assertEquals($sender->getId(), $result->getSender()->getId());
        self::assertEquals($recipient->getId(), $result->getRecipient()->getId());
    }
}
