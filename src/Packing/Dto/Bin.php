<?php

declare(strict_types=1);

namespace App\Packing\Dto;

use App\Entity\Packaging;

class Bin
{
    public function __construct(
        private readonly int $id,
        private readonly float $width,
        private readonly float $height,
        private readonly float $length,
        private readonly float $maxWeight,
    ) {
    }

    public static function fromPackaging(
        Packaging $packaging,
    ): self {
        assert($packaging->getId() > 0);
        assert($packaging->getWidth() > 0.0);
        assert($packaging->getHeight() > 0.0);
        assert($packaging->getLength() > 0.0);
        assert($packaging->getMaxWeight() > 0.0);

        return new self(
            $packaging->getId(),
            $packaging->getWidth(),
            $packaging->getHeight(),
            $packaging->getLength(),
            $packaging->getMaxWeight(),
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getMaxWeight(): float
    {
        return $this->maxWeight;
    }

    /**
     * @return array<string, int|float>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'h' => $this->height,
            'w' => $this->width,
            'd' => $this->length,
            'max_wg' => $this->maxWeight,
        ];
    }
}
