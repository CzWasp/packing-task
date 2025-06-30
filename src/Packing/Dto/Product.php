<?php

declare(strict_types=1);

namespace App\Packing\Dto;

use InvalidArgumentException;

class Product
{
    public function __construct(
        private readonly int $id,
        private readonly float $width,
        private readonly float $height,
        private readonly float $length,
        private readonly float $weight,
        private readonly int $quantity = 1,
        private readonly bool $verticalRotation = true,
    ) {
    }

    /**
     * @param mixed[] $data
     */
    public static function fromJson(array $data): self
    {
        foreach (['id', 'width', 'height', 'length', 'weight'] as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("Missing key: $key");
            }
        }

        if (
            !is_numeric($data['width']) || !is_numeric($data['height']) ||
            !is_numeric($data['length']) || !is_numeric($data['weight'])
        ) {
            throw new InvalidArgumentException("Dimensions and weight must be numeric.");
        }

        return new self(
            (int) $data['id'],
            (float) $data['width'],
            (float) $data['height'],
            (float) $data['length'],
            (float) $data['weight'],
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

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function isVerticalRotation(): bool
    {
        return $this->verticalRotation;
    }

    /**
     * @return array<string, int|float|bool>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'w' => $this->width,
            'h' => $this->height,
            'd' => $this->length,
            'wg' => $this->weight,
            'q' => $this->quantity,
            'vr' => $this->verticalRotation,
        ];
    }
}
