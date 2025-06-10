<?php

declare(strict_types=1);

namespace App\Tests\Unit\Transaction\Service;

use App\Account\Entity\Account;
use App\Currency\Entity\Currency;
use App\Currency\Enum\CurrencyCode;
use App\Currency\Enum\CurrencyRateSource;
use App\Currency\Exception\CurrencyRateNotFoundException;
use App\Currency\Service\CurrencyConversionService;
use App\Tests\DummyFactory\Account\AccountFactory;
use App\Tests\DummyFactory\Currency\CurrencyFactory;
use App\Transaction\Dto\TransferRequestDto;
use App\Transaction\Exception\InsufficientBalanceException;
use App\Transaction\Exception\InvalidTransferAccountException;
use App\Transaction\Exception\InvalidTransferAmountException;
use App\Transaction\Exception\InvalidTransferCurrencyException;
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
        $this->sut = new TransferValidationService($this->mockedConversionService, CurrencyRateSource::FAKE->value);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withNegativeAmount_throwsException(): void
    {
        $usdCurrency = CurrencyFactory::create(CurrencyCode::USD->value);

        $request = $this->createTransferRequest(
            AccountFactory::create(),
            AccountFactory::create(),
            -100,
            $usdCurrency,
        );

        self::expectExceptionObject(new InvalidTransferAmountException('Transfer amount must be greater than zero'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withInvalidCurrency_throwsException(): void
    {
        $angCurrency = CurrencyFactory::create(CurrencyCode::ANG->value);

        $request = $this->createTransferRequest(
            AccountFactory::create(),
            AccountFactory::create(),
            100,
            $angCurrency,
        );

        self::expectExceptionObject(new InvalidTransferCurrencyException('Invalid currency'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withSameAccount_throwsException(): void
    {
        $usdCurrency = CurrencyFactory::create(CurrencyCode::USD->value);

        $account = AccountFactory::create();
        $request = $this->createTransferRequest($account, $account, 100, $usdCurrency);

        self::expectExceptionObject(new InvalidTransferAccountException('Sender and recipient cannot be the same'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withInsufficientSenderFunds_throwsException(): void
    {
        $usdCurrency = CurrencyFactory::create(CurrencyCode::USD->value);

        $sender = AccountFactory::create(balance: 50);
        $receiver = AccountFactory::create(balance: 0);

        $request = $this->createTransferRequest($sender, $receiver, 100, $usdCurrency);

        $this->mockedConversionService
            ->expects(self::once())
            ->method('convert')
            ->with(100, CurrencyCode::USD, CurrencyCode::USD)
            ->willReturn(100);

        self::expectExceptionObject(new InsufficientBalanceException('Insufficient account balance'));

        $this->sut->validateTransferRequest($request);
    }

    /**
     * @throws InvalidTransferRequestException|CurrencyRateNotFoundException
     */
    public function testValidateTransferRequest_withValidRequest_doesNotThrowException(): void
    {
        $usdCurrency = CurrencyFactory::create(CurrencyCode::USD->value);

        $sender = AccountFactory::create(balance: 1000);
        $receiver = AccountFactory::create(balance: 0);

        $request = $this->createTransferRequest($sender, $receiver, 100, $usdCurrency);

        $this->mockedConversionService
            ->expects(self::once())
            ->method('convert')
            ->with(100, CurrencyCode::USD, CurrencyCode::USD)
            ->willReturn(100);

        $this->sut->validateTransferRequest($request);
    }

    private function createTransferRequest(
        Account $sender,
        Account $receiver,
        int $amount,
        Currency $currency,
    ): TransferRequestDto {
        return new TransferRequestDto(
            $sender,
            $receiver,
            $amount,
            $currency,
        );
    }
}
