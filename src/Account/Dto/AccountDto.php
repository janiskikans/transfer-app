<?php

declare(strict_types=1);

namespace App\Account\Dto;

use App\Currency\Enum\Currency;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

readonly class AccountDto
{
    public function __construct(
        public Uuid $id,
        public Currency $currency,
        #[OA\Property(type: 'float', example: 100.00)]
        public float $balance,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    )
    {
    }
}
