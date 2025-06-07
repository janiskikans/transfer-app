<?php

declare(strict_types=1);

namespace App\Tests\Unit\Transaction\Service;

use App\Account\Entity\Account;
use App\Currency\Enum\Currency;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InvalidTransferRequestException;
use App\Transaction\Service\TransferValidationService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransferValidationServiceTest extends TestCase
{
    private MockObject & CurrencyConversionService $mockedConversionService;
    private TransferValidationService $sut;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockedConversionService = $this->createMock(CurrencyConversionService::class);
        $this->sut = new TransferValidationService($this->mockedConversionService);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withNegativeAmount_throwsException(): void
    {
        $request = $this->createTransferRequest(AccountFactory::create(), AccountFactory::create(), -100);

        self::expectExceptionObject(new InvalidTransferRequestException('Transfer amount must be greater than zero.'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withSameAccount_throwsException(): void
    {
        $account = AccountFactory::create();
        $request = $this->createTransferRequest($account, $account, 100);

        self::expectExceptionObject(new InvalidTransferRequestException('Sender and recipient cannot be the same.'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withInsufficientSenderFunds_throwsException(): void
    {
        $sender = AccountFactory::create(balance: 50);
        $receiver = AccountFactory::create(balance: 0);

        $request = $this->createTransferRequest($sender, $receiver, 100);

        $this->mockedConversionService
            ->expects(self::once())
            ->method('convert')
            ->with(100, Currency::USD, Currency::USD)
            ->willReturn(100);

        self::expectExceptionObject(new InvalidTransferRequestException('Sender has insufficient funds.'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withValidRequest_doesNotThrowException(): void
    {
        $sender = AccountFactory::create(balance: 1000);
        $receiver = AccountFactory::create(balance: 0);

        $request = $this->createTransferRequest($sender, $receiver, 100);

        $this->mockedConversionService
            ->expects(self::once())
            ->method('convert')
            ->with(100, Currency::USD, Currency::USD)
            ->willReturn(100);

        $this->sut->validateTransferRequest($request);
    }

    private function createTransferRequest(
        Account $sender,
        Account $receiver,
        int $amount,
        Currency $currency = Currency::USD,
    ): TransferRequestDto {
        return new TransferRequestDto(
            $sender,
            $receiver,
            $amount,
            $currency
        );
    }
}
