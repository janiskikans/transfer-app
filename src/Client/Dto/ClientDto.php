<?php

declare(strict_types=1);

namespace App\Client\Dto;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

readonly class ClientDto
{
    public function __construct(
        public Uuid $id,
        #[OA\Property(type: 'string', example: 'john.doe@test.com')]
        public string $email,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
