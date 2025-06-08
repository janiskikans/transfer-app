<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Helper;

use App\Shared\Helper\MoneyAmountHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyAmountHelperTest extends TestCase
{
    #[DataProvider('provideMinorAmounts')]
    public function testConvertToMajor_correctlyConvertsToMajorUnits(
        int $minorAmount,
        int $decimalPlaces,
        float $expectedMajorAmount,
    ): void {
        $result = MoneyAmountHelper::convertToMajor($minorAmount, $decimalPlaces);
        self::assertEquals($expectedMajorAmount, $result);
    }

    public static function provideMinorAmounts(): array
    {
        return [
            [1000, 2, 10],
            [1000, 1, 100],
            [1000, 0, 1000],
            [1250, 2, 12.5],
        ];
    }

    #[DataProvider('provideMajorAmounts')]
    public function testConvertToMinor_correctlyConvertsToMinorUnits(
        float $majorAmount,
        int $decimalPlaces,
        int $expectedMinorAmount,
    ): void {
        $result = MoneyAmountHelper::convertToMinor($majorAmount, $decimalPlaces);
        self::assertEquals($expectedMinorAmount, $result);
    }

    public static function provideMajorAmounts(): array
    {
        return [
            [10, 2, 1000],
            [100, 1, 1000],
            [1000, 0, 1000],
            [12.5, 2, 1250],
        ];
    }
}
