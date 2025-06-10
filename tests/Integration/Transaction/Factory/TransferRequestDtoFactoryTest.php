<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction\Factory;

use App\Account\Entity\Account;
use App\Account\Repository\AccountRepositoryInterface;
use App\Client\Entity\Client;
use App\Currency\Entity\Currency;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Client\ClientFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\Integration\HasEntityManager;
use App\Transaction\Dto\TransferPostRequestDto;
use App\Transaction\Factory\TransferRequestDtoFactory;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class TransferRequestDtoFactoryTest extends KernelTestCase
{
    use HasEntityManager;

    private TransferRequestDtoFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();

        $this->sut = new TransferRequestDtoFactory(
            self::getContainer()->get(AccountRepositoryInterface::class),
            self::getContainer()->get(CurrencyRepositoryInterface::class),
        );
    }

    public function testFromTransferPostRequest_withoutValidSenderAccount_throwsException(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $recipientAccount = $this->createAccount($currency, $client);

        $request = $this->buildTransferPostRequestDto(
            senderAccountId: Uuid::v6()->toString(),
            recipientAccountId: $recipientAccount->getId()->toString(),
            amount: 10.20,
            currency: $currency->getCode()->value,
        );

        self::expectExceptionObject(new RuntimeException('Sender account not found'));

        $this->sut->fromTransferPostRequest($request);
    }

    public function testFromTransferPostRequest_withoutValidRecipientAccount_throwsException(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $senderAccount = $this->createAccount($currency, $client);

        $request = $this->buildTransferPostRequestDto(
            senderAccountId: $senderAccount->getId()->toString(),
            recipientAccountId: Uuid::v6()->toString(),
            amount: 10.20,
            currency: $currency->getCode()->value,
        );

        self::expectExceptionObject(new RuntimeException('Recipient account not found'));

        $this->sut->fromTransferPostRequest($request);
    }

    public function testFromTransferPostRequest_withoutValidCurrency_throwsException(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $senderAccount = $this->createAccount($currency, $client);
        $recipientAccount = $this->createAccount($currency, $client);

        $request = $this->buildTransferPostRequestDto(
            senderAccountId: $senderAccount->getId()->toString(),
            recipientAccountId: $recipientAccount->getId()->toString(),
            amount: 10.20,
            currency: 'FKE',
        );

        self::expectExceptionObject(new RuntimeException('Invalid currency given'));

        $this->sut->fromTransferPostRequest($request);
    }

    public function testFromTransferPostRequest_withValidCurrency_returnsCorrectDto(): void
    {
        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $senderAccount = $this->createAccount($currency, $client);
        $recipientAccount = $this->createAccount($currency, $client);

        $request = $this->buildTransferPostRequestDto(
            senderAccountId: $senderAccount->getId()->toString(),
            recipientAccountId: $recipientAccount->getId()->toString(),
            amount: 10.34,
            currency: 'USD',
        );

        $dto = $this->sut->fromTransferPostRequest($request);

        self::assertEquals(1034, $dto->getAmount());
        self::assertEquals($senderAccount->getId(), $dto->getSender()->getId());
        self::assertEquals($recipientAccount->getId(), $dto->getRecipient()->getId());
        self::assertEquals(CurrencyCode::USD, $dto->getCurrency()->getCode());
    }

    private function buildTransferPostRequestDto(
        string $senderAccountId,
        string $recipientAccountId,
        float $amount,
        string $currency,
    ): TransferPostRequestDto {
        return new TransferPostRequestDto(
            $senderAccountId,
            $recipientAccountId,
            $amount,
            $currency,
        );
    }

    private function createAccount(
        Currency $currency,
        Client $client,
    ): Account {
        $account = AccountFactory::create(client: $client, currency: $currency);
        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }
}
