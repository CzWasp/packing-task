<?php

declare(strict_types=1);

namespace App;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Packing\Dto\Product;
use App\Packing\Dto\ProductList;
use App\Packing\Dto\Bin;
use App\Packing\Dto\BinList;
use App\Packing\Dto\PackingData;
use App\Packing\Dto\BestBox;
use App\Packing\FallbackPackingCalculator;
use RuntimeException;

class FallbackPackingCalculatorTest extends TestCase
{
    #[DataProvider('provideValidPackingData')]
    public function testReturnsBestBin(PackingData $packingData, int $expectedBinId): void
    {
        $calculator = new FallbackPackingCalculator();

        $bestBox = $calculator->getBestBoxForPackingData($packingData);

        $this->assertInstanceOf(BestBox::class, $bestBox);
        $this->assertSame($expectedBinId, $bestBox->getId());
    }

    /**
     * @return array<string, array<int, PackingData|int>>
     */
    public static function provideValidPackingData(): array
    {
        return [
            'fits in smaller bin' => [
                new PackingData(
                    new ProductList([
                        new Product(1, 5, 5, 5, 1.0, 1),
                    ]),
                    new BinList([
                        new Bin(1, 10, 10, 10, 10.0),
                        new Bin(2, 15, 15, 15, 20.0),
                    ])
                ),
                1
            ],
            'fits only in larger bin' => [
                new PackingData(
                    new ProductList([
                        new Product(1, 10, 10, 10, 5.0, 1),
                    ]),
                    new BinList([
                        new Bin(3, 8, 8, 8, 10.0),
                        new Bin(4, 15, 15, 15, 10.0),
                    ])
                ),
                4
            ],
        ];
    }

    #[DataProvider('provideInvalidPackingData')]
    public function testThrowsWhenNoBinSuitable(PackingData $packingData): void
    {
        $calculator = new FallbackPackingCalculator();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No suitable bins found');

        $calculator->getBestBoxForPackingData($packingData);
    }

    /**
     * @return array<string, array<int, PackingData>>
     */
    public static function provideInvalidPackingData(): array
    {
        return [
            'too heavy' => [
                new PackingData(
                    new ProductList([
                        new Product(1, 5, 5, 5, 100.0, 1),
                    ]),
                    new BinList([
                        new Bin(10, 10, 10, 10, 50.0),
                    ])
                ),
            ],
            'too large' => [
                new PackingData(
                    new ProductList([
                        new Product(2, 50, 50, 50, 10.0, 1),
                    ]),
                    new BinList([
                        new Bin(11, 30, 30, 30, 100.0),
                    ])
                ),
            ],
        ];
    }
}
