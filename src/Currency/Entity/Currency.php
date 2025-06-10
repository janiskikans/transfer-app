<?php

declare(strict_types=1);

namespace App\Currency\Entity;

use App\Currency\Enum\CurrencyCode;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'currency')]
class Currency
{
    public function __construct(
        #[Id]
        #[Column(type: 'string', length: 3, enumType: CurrencyCode::class)]
        private ?CurrencyCode $code = null,
        #[Column(type: 'string', length: 100)]
        private ?string $name = null,
        #[Column(type: 'integer')]
        private ?int $decimalPlaces = null,
    ) {
    }

    public function getCode(): CurrencyCode
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDecimalPlaces(): int
    {
        return $this->decimalPlaces;
    }
}
