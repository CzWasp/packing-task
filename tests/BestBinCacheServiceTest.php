<?php

declare(strict_types=1);

namespace App;

use App\Packing\BestBinCacheService;
use App\Packing\Dto\Product;
use App\Packing\Dto\ProductList;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BestBinCacheServiceTest extends TestCase
{
    private BestBinCacheService $service;

    protected function setUp(): void
    {
        $this->service = new BestBinCacheService();
    }

    #[DataProvider('equivalentProductListsProvider')]
    public function testEquivalentProductListsProduceSameKey(ProductList $listA, ProductList $listB): void
    {
        $keyA = $this->service->getCacheKey($listA);
        $keyB = $this->service->getCacheKey($listB);

        Assert::assertSame($keyA, $keyB);
    }

    /**
     * @return array<string, ProductList[]>
     */
    public static function equivalentProductListsProvider(): array
    {
        return [
            'same products different order' => [
                new ProductList([
                    new Product(1, 3.4, 2.1, 3.0, 4.0),
                    new Product(2, 4.9, 1.0, 2.4, 9.9),
                ]),
                new ProductList([
                    new Product(2, 4.9, 1.0, 2.4, 9.9),
                    new Product(1, 3.4, 2.1, 3.0, 4.0),
                ]),
            ],
            'dimensions with rounding difference that rounds equally' => [
                new ProductList([
                    new Product(1, 1.003, 2.0, 3.0, 1.0),
                ]),
                new ProductList([
                    new Product(1, 1.001, 2.0, 3.0, 1.0),
                ]),
            ],
        ];
    }

    #[DataProvider('differentProductListsProvider')]
    public function testDifferentProductListsProduceDifferentKey(ProductList $listA, ProductList $listB): void
    {
        $keyA = $this->service->getCacheKey($listA);
        $keyB = $this->service->getCacheKey($listB);

        Assert::assertNotSame($keyA, $keyB);
    }

    /**
     * @return array<string, ProductList[]>
     */
    public static function differentProductListsProvider(): array
    {
        return [
            'different quantity' => [
                new ProductList([
                    new Product(1, 1.0, 1.0, 1.0, 1.0, 1),
                ]),
                new ProductList([
                    new Product(1, 1.0, 1.0, 1.0, 1.0, 2),
                ]),
            ],
            'rounding results in different side lengths' => [
                new ProductList([
                    new Product(1, 1.004, 2.0, 3.0, 1.0),
                ]),
                new ProductList([
                    new Product(1, 1.015, 2.0, 3.0, 1.0),
                ]),
            ],
        ];
    }

    public function testKeyIsStable(): void
    {
        $product = new Product(1, 1.111, 2.222, 3.333, 4.444, 3, false);
        $list = new ProductList([$product]);

        $key1 = $this->service->getCacheKey($list);
        $key2 = $this->service->getCacheKey($list);

        Assert::assertSame($key1, $key2);
    }
}
