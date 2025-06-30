<?php

declare(strict_types=1);

namespace App\Packing\Dto;

class BestBoxResult
{
    public function __construct(
        private readonly BestBox $bestBox,
        private readonly bool $isLoadedFromCache,
        private readonly string $cacheKey,
    ) {
        assert(trim($cacheKey) !== '');
    }

    public function getBestBox(): BestBox
    {
        return $this->bestBox;
    }

    public function isLoadedFromCache(): bool
    {
        return $this->isLoadedFromCache;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }
}
