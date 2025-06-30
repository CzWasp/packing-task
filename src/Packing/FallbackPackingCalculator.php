<?php

declare(strict_types=1);

namespace App\Packing;

use App\Packing\Dto\BestBox;
use App\Packing\Dto\Bin;
use App\Packing\Dto\PackingData;
use App\Packing\Dto\ProductMetrics;

class FallbackPackingCalculator implements PackingInterface
{
    public function getBestBoxForPackingData(PackingData $packingData): BestBox
    {
        $metrics = $this->calculateMetrics($packingData->getProductList()->getProducts());
        $suitableBins = $this->filterSuitableBins($packingData->getBinList()->getBins(), $metrics);

        return $this->selectBestBin($suitableBins);
    }

    /**
     * @param \App\Packing\Dto\Product[] $products
     * @return \App\Packing\Dto\ProductMetrics
     */
    private function calculateMetrics(array $products): ProductMetrics
    {
        $totalWeight = 0.0;
        $totalVolume = 0.0;
        $maxWidth = 0.0;
        $maxHeight = 0.0;
        $maxLength = 0.0;

        foreach ($products as $product) {
            $q = $product->getQuantity();
            $totalWeight += $product->getWeight() * $q;
            $totalVolume += $product->getWidth() * $product->getHeight() * $product->getLength() * $q;

            $maxWidth = max($maxWidth, $product->getWidth());
            $maxHeight = max($maxHeight, $product->getHeight());
            $maxLength = max($maxLength, $product->getLength());
        }

        return new ProductMetrics(
            $totalWeight,
            $totalVolume,
            $maxWidth,
            $maxHeight,
            $maxLength,
        );
    }

    /**
     * @param Bin[] $bins
     * @return Bin[]
     */
    private function filterSuitableBins(array $bins, ProductMetrics $metrics): array
    {
        return array_filter($bins, function (Bin $bin) use ($metrics) {
            $binVolume = $bin->getWidth() * $bin->getHeight() * $bin->getLength();

            return (
                $bin->getMaxWeight() >= $metrics->getTotalWeight() &&
                $binVolume >= $metrics->getTotalVolume() &&
                $bin->getWidth() >= $metrics->getMaxWidth() &&
                $bin->getHeight() >= $metrics->getMaxHeight() &&
                $bin->getLength() >= $metrics->getMaxLength()
            );
        });
    }

    /**
     * @param Bin[] $bins
     */
    private function selectBestBin(array $bins): BestBox
    {
        if (empty($bins)) {
            throw new \RuntimeException('No suitable bins found');
        }

        usort($bins, function (Bin $a, Bin $b) {
            $volumeA = $a->getWidth() * $a->getHeight() * $a->getLength();
            $volumeB = $b->getWidth() * $b->getHeight() * $b->getLength();
            return $volumeA <=> $volumeB;
        });

        return new BestBox($bins[0]->getId());
    }
}
