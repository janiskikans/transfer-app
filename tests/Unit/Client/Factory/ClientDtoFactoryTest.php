<?php

declare(strict_types=1);

namespace App\Tests\Unit\Client\Factory;

use App\Client\Factory\ClientDtoFactory;
use App\Tests\DummyFactory\Client\ClientFactory;
use PHPUnit\Framework\TestCase;

class ClientDtoFactoryTest extends TestCase
{
    private ClientDtoFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new ClientDtoFactory();
    }

    public function testCreateFromEntity_returnsCorrectDto(): void
    {
        $client = ClientFactory::create();

        $result = $this->sut->createFromEntity($client);

        self::assertEquals($client->getId(), $result->id);
        self::assertEquals($client->getEmail(), $result->email);
        self::assertEquals($client->getCreatedAt(), $result->createdAt);
    }
}
