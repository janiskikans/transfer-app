<?php

namespace App\Currency\Repository;

use App\Currency\Entity\Currency;

interface CurrencyRepositoryInterface
{
    public function getByCode(string $code): ?Currency;
}
