<?php

declare(strict_types=1);

namespace App\Transaction\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class TransferPostRequestDto
{
    public function __construct(
        #[Assert\Uuid(message: 'Invalid sender account ID')]
        #[Assert\NotBlank(message: 'Sender account ID is required')]
        #[Assert\Type(type: 'string', message: 'Sender account ID must be string')]
        #[OA\Property(type: 'string', format: 'uuid')]
        public $senderAccountId,
        #[Assert\Uuid(message: 'Invalid recipient ID')]
        #[Assert\NotBlank(message: 'Recipient ID is required')]
        #[Assert\Type(type: 'string', message: 'Recipient account ID must be string')]
        #[OA\Property(type: 'string', format: 'uuid')]
        public $recipientAccountId,
        #[Assert\Positive(message: 'Amount must be positive')]
        #[Assert\NotBlank(message: 'Amount is required')]
        #[Assert\Type(type: 'number', message: 'Amount must be number')]
        #[OA\Property(type: 'number', example: 100.00)]
        public $amount,
        #[Assert\NotBlank(message: 'Currency is required')]
        #[Assert\Currency(message: 'Invalid currency code')]
        #[Assert\Type(type: 'string', message: 'Currency must be string')]
        #[OA\Property(type: 'string', example: 'USD')]
        public $currency,
    ) {
    }
}
