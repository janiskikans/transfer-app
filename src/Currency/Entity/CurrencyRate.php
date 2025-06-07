<?php

declare(strict_types=1);

namespace App\Currency\Entity;

use App\Currency\Enum\CurrencyRateSource;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Timestampable;

#[Entity]
#[Table(name: 'currency_rate')]
#[Index(name: 'currency_source_idx', columns: ['source'])]
#[Index(name: 'currency_updated_at_idx', columns: ['updated_at'])]
class CurrencyRate
{
    public function __construct(
        #[Id]
        #[GeneratedValue]
        #[Column]
        private ?int $id = null,
        #[Column(type: 'string', length: 50, enumType: CurrencyRateSource::class)]
        private ?CurrencyRateSource $source = null,
        #[ManyToOne(targetEntity: Currency::class)]
        #[JoinColumn(name: 'base_currency', referencedColumnName: 'code', nullable: false)]
        private ?Currency $baseCurrency = null,
        #[ManyToOne(targetEntity: Currency::class)]
        #[JoinColumn(name: 'target_currency', referencedColumnName: 'code', nullable: false)]
        private ?Currency $targetCurrency = null,
        #[Column(type: 'decimal', precision: 12, scale: 5)]
        private ?float $rate = null,
        #[Column(type: 'datetime_immutable')]
        #[Timestampable(on: 'update')]
        private ?DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSource(): CurrencyRateSource
    {
        return $this->source;
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): Currency
    {
        return $this->targetCurrency;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
