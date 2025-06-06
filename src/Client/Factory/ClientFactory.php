<?php

namespace App\Client\Factory;

use App\Client\Entity\Client;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
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
        $now = new \DateTimeImmutable();

        return [
            'id' => Uuid::v6(),
            'email' => self::faker()->email(),
            'createdAt' => $now,
            'updatedAt' => $now,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(User $user): void {})
            ;
    }
}
