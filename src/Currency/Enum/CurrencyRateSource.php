<?php

declare(strict_types=1);

namespace App\Currency\Enum;

enum CurrencyRateSource: string
{
    case EXCHANGE_RATE_HOST = 'ERH';
}
