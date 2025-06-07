<?php

declare(strict_types=1);

namespace App\Account\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $message = 'Insufficient account balance';
}
