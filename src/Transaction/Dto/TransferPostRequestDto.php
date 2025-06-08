<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TransferPostRequestDto
{
    public function __construct(
        #[Assert\Uuid(message: 'Invalid sender account ID')]
        #[Assert\NotBlank(message: 'Sender account ID is required')]
        #[Assert\Type(type: 'string', message: 'Sender account ID must be string')]
        public $senderAccountId,
        #[Assert\Uuid(message: 'Invalid recipient ID')]
        #[Assert\NotBlank(message: 'Recipient ID is required')]
        #[Assert\Type(type: 'string', message: 'Recipient account ID must be string')]
        public $recipientAccountId,
        #[Assert\Positive(message: 'Amount must be positive')]
        #[Assert\NotBlank(message: 'Amount is required')]
        #[Assert\Type(type: 'float', message: 'Amount must be float')]
        public $amount,
        #[Assert\NotBlank(message: 'Currency is required')]
        #[Assert\Currency(message: 'Invalid currency code')]
        #[Assert\Type(type: 'string', message: 'Currency must be string')]
        public $currency,
    ) {
    }
}
