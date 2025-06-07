<?php

declare(strict_types=1);

namespace App\Currency\Exception;

use Exception;

class CurrencyRateNotFoundException extends Exception
{
    protected $message = 'Currency rate not found';
}
