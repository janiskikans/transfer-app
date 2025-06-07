<?php

declare(strict_types=1);

namespace App\Transaction\Exception;

use Exception;

class TransferFailedException extends Exception
{
    protected $message = 'Fund transfer failed';
}
