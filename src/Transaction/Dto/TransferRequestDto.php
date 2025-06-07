<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

readonly class TransferRequestDto
{
    public function __construct(
        string $senderId,
        string $recipientId,
        float $amount,
    ) {
        // TODO: Some readable validation messages?
    }
}
