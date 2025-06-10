<?php

declare(strict_types=1);

namespace App\Tests\Integration\Account\Repository\Doctrine;

use App\Account\Repository\Doctrine\AccountRepository;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Client\ClientFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Tests\Integration\HasEntityManager;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class AccountRepositoryTest extends KernelTestCase
{
    use HasEntityManager;

    private AccountRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
        $this->sut = self::getContainer()->get(AccountRepository::class);
    }

    public function testGetByClientId_withExistingClientAccount_returnsLinkedAccounts(): void
    {
        $client = ClientFactory::create();
        $this->entityManager->persist($client);

        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);

        $account = AccountFactory::create(currency: $currency);
        $client->addAccount($account);

        $this->entityManager->flush();

        $result = $this->sut->getByClientId($client->getId()->toString());

        self::assertNotNull($account);
        self::assertCount(1, $result);
        self::assertEquals($account->getId(), $result[0]->getId());
    }

    public function testGetByClientId_withoutExistingClientAccount_returnsEmptyArray(): void
    {
        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $result = $this->sut->getByClientId($client->getId()->toString());

        self::assertEmpty($result);
    }

    public function testGetById_withoutExistingAccount_returnsNull(): void
    {
        self::assertNull($this->sut->getById(Uuid::v6()->toString()));
    }

    public function testGetById_withExistingAccount_returnsAccount(): void
    {
        $client = ClientFactory::create();
        $this->entityManager->persist($client);

        $currency = CurrencyFactory::create();
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        $account = AccountFactory::create(client: $client, currency: $currency);
        $this->entityManager->persist($account);
        $this->entityManager->flush();

        $result = $this->sut->getById($account->getId()->toString());
        self::assertNotNull($result);
        self::assertEquals($account->getId(), $result->getId());
        self::assertEquals(10000, $result->getBalance());
        self::assertEquals($currency->getCode(), $result->getCurrency()->getCode());
        self::assertEquals($client->getId(), $result->getClient()->getId());
        self::assertInstanceOf(DateTimeImmutable::class, $result->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $result->getUpdatedAt());
    }
}
