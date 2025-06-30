<?php

declare(strict_types=1);

namespace App\Packing;

use App\Entity\PackagingCache;
use App\Packing\Dto\BestBox;
use App\Packing\Dto\BestBoxResult;
use App\Packing\Dto\PackingData;
use Closure;
use Exception;

class PackingService
{
    public function __construct(
        private readonly PackingInterface $packingApiCalculator,
        private readonly PackingInterface $fallbackPackingCalculator,
        private readonly BestBinCacheService $bestBinCacheService,
    ) {
    }

    /**
     * @param \App\Packing\Dto\PackingData $packingData
     * @param \Closure(string $cacheKey): ?PackagingCache $findPackagingCacheCallback
     * @return \App\Packing\Dto\BestBoxResult
     */
    public function packIntoBestBox(
        PackingData $packingData,
        Closure $findPackagingCacheCallback,
    ): BestBoxResult {
        $cacheKey = $this->bestBinCacheService->getCacheKey($packingData->getProductList());
        $bestBoxInCache = $findPackagingCacheCallback($cacheKey);
        if ($bestBoxInCache !== null) {
            return new BestBoxResult(
                new BestBox($bestBoxInCache->getPackaging()->getId()),
                true,
                $cacheKey
            );
        }

        try {
            $bestBox = $this->packingApiCalculator->getBestBoxForPackingData($packingData);
        } catch (Exception $e) {
            $bestBox = $this->fallbackPackingCalculator->getBestBoxForPackingData($packingData);
        }

        return new BestBoxResult(
            $bestBox,
            false,
            $cacheKey,
        );
    }
}
