<?php

declare(strict_types=1);

namespace App\Currency\Entity;

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
        #[Column(type: 'string', length: 3)]
        private string $code,
        #[Column(type: 'string', length: 100)]
        private string $name,
        #[Column(type: 'integer')]
        private int $decimalPlaces,
    ) {
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function decimalPlaces(): int
    {
        return $this->decimalPlaces;
    }
}
