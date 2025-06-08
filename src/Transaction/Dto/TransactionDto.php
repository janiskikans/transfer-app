<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

use App\Transaction\Enum\TransactionType;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

readonly class TransactionDto
{
    public function __construct(
        public Uuid $id,
        public Uuid $sender,
        public Uuid $recipient,
        #[OA\Property(type: 'float', example: 100.00)]
        public float $amount,
        #[OA\Property(type: 'string', example: 'USD')]
        public string $currency,
        public TransactionType $type,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
