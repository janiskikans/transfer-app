<?php

declare(strict_types=1);

namespace App\Transaction\Enum;

enum TransactionType: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
