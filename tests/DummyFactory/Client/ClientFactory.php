<?php

declare(strict_types=1);

namespace App\Tests\DummyFactory\Client;

use App\Client\Entity\Client;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class ClientFactory
{
    public static function create(
        string $email = 'john.doe@test.com',
        DateTimeImmutable $createdAt = new DateTimeImmutable()
    ): Client {
        return new Client(
            id: Uuid::v6(),
            email: $email,
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );
    }
}
