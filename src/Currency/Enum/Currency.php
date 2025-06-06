<?php

declare(strict_types=1);

namespace App\Currency\Enum;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
}
