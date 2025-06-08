<?php

declare(strict_types=1);

namespace App\Client\Dto;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

readonly class ClientDto
{
    public function __construct(
        public Uuid $id,
        public string $email,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
