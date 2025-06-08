<?php

declare(strict_types=1);

namespace App\Transaction\Exception;

class InsufficientBalanceException extends InvalidTransferRequestException
{
    protected $message = 'Insufficient account balance';
}
