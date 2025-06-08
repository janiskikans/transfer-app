<?php

declare(strict_types=1);

namespace App\Account\Factory;

use App\Account\Dto\AccountDto;
use App\Account\Entity\Account;
use App\Shared\Helper\MoneyAmountHelper;

class AccountDtoFactory
{
    public function createFromEntity(Account $account): AccountDto
    {
        $currency = $account->getCurrency();

        return new AccountDto(
            $account->getId(),
            $currency->toEnum(),
            MoneyAmountHelper::convertToMajor($account->getBalance(), $currency->getDecimalPlaces()),
            $account->getCreatedAt(),
            $account->getUpdatedAt(),
        );
    }
}
