<?php

declare(strict_types=1);

namespace App\Tests\Integration\Client\Repository\Doctrine;

use App\Client\Repository\Doctrine\ClientRepository;
use App\Tests\DummyFactory\Client\ClientFactory;
use App\Tests\Integration\HasEntityManager;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

use function PHPUnit\Framework\assertNull;

class ClientRepositoryTest extends KernelTestCase
{
    use HasEntityManager;

    private ClientRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEntityManager();
        $this->sut = self::getContainer()->get(ClientRepository::class);
    }

    public function testGetById_withoutExistingClient_returnsNull(): void
    {
        self:assertNull($this->sut->getById(Uuid::v6()->toString()));
    }

    public function testGetById_withExistingClient_returnsClient(): void
    {
        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $result = $this->sut->getById($client->getId()->toString());
        self::assertNotNull($result);
        self::assertEquals($client->getId(), $result->getId());
        self::assertEquals($client->getEmail(), $result->getEmail());
        self::assertInstanceOf(DateTimeImmutable::class, $result->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $result->getUpdatedAt());
    }

    public function testGetAll_withoutExistingClients_returnsEmptyArray(): void
    {
        $result = $this->sut->getAll();
        self::assertEmpty($result);
    }

    public function testGetAll_withExistingClients_returnsClientArray(): void
    {
        $client = ClientFactory::create();
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $result = $this->sut->getAll();
        self::assertCount(1, $result);
        self::assertEquals($client->getId(), $result[0]->getId());
    }
}
