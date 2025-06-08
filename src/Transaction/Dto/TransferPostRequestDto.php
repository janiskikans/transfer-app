<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class TransferPostRequestDto
{
    // TODO: Account context in names
    public function __construct(
        #[Assert\Uuid(message: 'Invalid sender ID')]
        #[Assert\NotBlank(message: 'Sender ID is required')]
        public string $senderId,
        #[Assert\Uuid(message: 'Invalid recipient ID')]
        #[Assert\NotBlank(message: 'Recipient ID is required')]
        public string $recipientId,
        #[Assert\Positive(message: 'Amount must be positive')]
        #[Assert\NotBlank(message: 'Amount is required')]
        public float $amount,
        #[Assert\NotBlank(message: 'Currency is required')]
        #[Assert\Currency(message: 'Invalid currency code')]
        public string $currency,
    ) {
    }
}
