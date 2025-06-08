<?php

declare(strict_types=1);

namespace App\Account\Dto;

use App\Currency\Enum\Currency;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

readonly class AccountDto
{
    public function __construct(
        public Uuid $id,
        public Currency $currency,
        public float $balance,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    )
    {
    }
}
