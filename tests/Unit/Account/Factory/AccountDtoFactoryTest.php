<?php

declare(strict_types=1);

namespace App\Tests\Unit\Account\Factory;

use App\Account\Factory\AccountDtoFactory;
use App\Tests\DummyFactory\Account\AccountFactory;
use PHPUnit\Framework\TestCase;

class AccountDtoFactoryTest extends TestCase
{
    private AccountDtoFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccountDtoFactory();
    }

    public function testCreateFromEntity_returnsCorrectDto(): void
    {
        $account = AccountFactory::create();

        $dto = $this->sut->createFromEntity($account);

        self::assertEquals($account->getId(), $dto->id);
        self::assertEquals($account->getCurrency()->toEnum(), $dto->currency);
        self::assertEquals(100.00, $dto->balance);
        self::assertEquals($account->getCreatedAt(), $dto->createdAt);
        self::assertEquals($account->getUpdatedAt(), $dto->updatedAt);
    }
}
