<?php

declare(strict_types=1);

namespace App\Client\Factory;

use App\Client\Dto\ClientDto;
use App\Client\Entity\Client;

readonly class ClientDtoFactory
{
    public function createFromEntity(Client $client): ClientDto
    {
        return new ClientDto(
            $client->getId(),
            $client->getEmail(),
            $client->getCreatedAt(),
        );
    }
}
