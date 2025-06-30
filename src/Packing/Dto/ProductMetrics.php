<?php

declare(strict_types=1);

namespace App\Packing\Dto;

class ProductMetrics
{
    public function __construct(
        private readonly float $totalWeight,
        private readonly float $totalVolume,
        private readonly float $maxLength,
        private readonly float $maxWidth,
        private readonly float $maxHeight,
    ) {
    }

    public function getMaxHeight(): float
    {
        return $this->maxHeight;
    }

    public function getMaxWidth(): float
    {
        return $this->maxWidth;
    }

    public function getMaxLength(): float
    {
        return $this->maxLength;
    }

    public function getTotalVolume(): float
    {
        return $this->totalVolume;
    }

    public function getTotalWeight(): float
    {
        return $this->totalWeight;
    }
}
