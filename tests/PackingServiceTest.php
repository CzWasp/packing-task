<?php

declare(strict_types=1);

namespace App\Tests\Packing;

use App\Packing\PackingService;
use App\Packing\PackingInterface;
use App\Packing\BestBinCacheService;
use App\Packing\Dto\PackingData;
use App\Packing\Dto\Product;
use App\Packing\Dto\ProductList;
use App\Packing\Dto\Bin;
use App\Packing\Dto\BinList;
use App\Packing\Dto\BestBox;
use App\Packing\Dto\BestBoxResult;
use App\Entity\Packaging;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Exception;

class PackingServiceTest extends TestCase
{
    private PackingInterface&MockObject $apiCalculator;
    private PackingInterface&MockObject $fallbackCalculator;
    private BestBinCacheService&MockObject $cacheService;

    private PackingService $service;

    protected function setUp(): void
    {
        $this->apiCalculator = $this->createMock(PackingInterface::class);
        $this->fallbackCalculator = $this->createMock(PackingInterface::class);
        $this->cacheService = $this->createMock(BestBinCacheService::class);

        $this->service = new PackingService(
            $this->apiCalculator,
            $this->fallbackCalculator,
            $this->cacheService
        );
    }

    public function testReturnsCachedResult(): void
    {
        $cacheKey = 'abc123';
        $packingData = $this->createPackingData();

        $packaging = $this->createMock(\App\Entity\Packaging::class);
        $packaging->method('getId')->willReturn(99);

        $packagingCache = $this->createMock(\App\Entity\PackagingCache::class);
        $packagingCache->method('getPackaging')->willReturn($packaging);

        $this->cacheService->method('getCacheKey')->willReturn($cacheKey);

        $findPackagingCacheCallback = function (string $key) use ($cacheKey, $packagingCache): ?\App\Entity\PackagingCache {
            return $key === $cacheKey ? $packagingCache : null;
        };

        $result = $this->service->packIntoBestBox(
            $packingData,
            $findPackagingCacheCallback
        );

        $this->assertTrue($result->isLoadedFromCache());
        $this->assertSame(99, $result->getBestBox()->getId());
        $this->assertSame($cacheKey, $result->getCacheKey());
    }

    public function testUsesApiCalculator(): void
    {
        $packingData = $this->createPackingData();
        $cacheKey = 'no-hit';

        $this->cacheService->method('getCacheKey')->willReturn($cacheKey);
        $this->apiCalculator->method('getBestBoxForPackingData')->willReturn(new BestBox(42));

        $result = $this->service->packIntoBestBox($packingData, fn() => null);

        $this->assertFalse($result->isLoadedFromCache());
        $this->assertSame(42, $result->getBestBox()->getId());
    }

    public function testUsesFallbackOnApiFailure(): void
    {
        $packingData = $this->createPackingData();
        $cacheKey = 'fallback-key';

        $this->cacheService->method('getCacheKey')->willReturn($cacheKey);
        $this->apiCalculator->method('getBestBoxForPackingData')->willThrowException(new Exception('API failed'));
        $this->fallbackCalculator->method('getBestBoxForPackingData')->willReturn(new BestBox(7));

        $result = $this->service->packIntoBestBox($packingData, fn() => null);

        $this->assertFalse($result->isLoadedFromCache());
        $this->assertSame(7, $result->getBestBox()->getId());
    }

    private function createPackingData(): PackingData
    {
        return new PackingData(
            new ProductList([
                new Product(1, 5.0, 5.0, 5.0, 1.0, 1),
            ]),
            new BinList([
                new Bin(1, 10.0, 10.0, 10.0, 10.0),
            ])
        );
    }
}
