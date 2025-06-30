<?php

declare(strict_types=1);

namespace App\Packing\Dto;

use App\Entity\Packaging;

class BinList
{
    /**
     * @param Bin[] $bins
     */
    public function __construct(
        private array $bins
    ) {
    }

    /**
     * @param Packaging[] $packagingList
     */
    public static function fromPackagingList(array $packagingList): self
    {
        assert(count($packagingList) > 0);

        $bins = [];
        foreach ($packagingList as $packaging) {
            $bins[] = Bin::fromPackaging($packaging);
        }

        return new self($bins);
    }

    /**
     * @return Bin[]
     */
    public function getBins(): array
    {
        return $this->bins;
    }

    /**
     * @return array<int, array<string, int|float>>
     */
    public function toArray(): array
    {
        return array_map(fn(Bin $bin) => $bin->toArray(), $this->bins);
    }
}
