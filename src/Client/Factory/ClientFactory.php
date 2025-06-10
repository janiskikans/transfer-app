<?php

namespace App\Client\Factory;

use App\Client\Entity\Client;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @codeCoverageIgnore
 * @extends PersistentProxyObjectFactory<Client>
 */
final class ClientFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Client::class;
    }

    protected function defaults(): array | callable
    {
        $now = new DateTimeImmutable();

        return [
            'id' => Uuid::v6(),
            'email' => self::faker()->email(),
            'createdAt' => $now,
            'updatedAt' => $now,
        ];
    }
}
