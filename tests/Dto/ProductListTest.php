<?php

declare(strict_types=1);

namespace App\Dto;

use App\Packing\Dto\ProductList;
use App\Packing\Dto\Product;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductListTest extends TestCase
{
    public function testFromJsonCreatesProductList(): void
    {
        $json = [
            'products' => [
                [
                    'id' => 1,
                    'width' => 3.4,
                    'height' => 2.1,
                    'length' => 3.0,
                    'weight' => 4.0,
                ],
                [
                    'id' => 2,
                    'width' => 4.9,
                    'height' => 1.0,
                    'length' => 2.4,
                    'weight' => 9.9,
                ],
            ],
        ];

        $productList = ProductList::fromJson($json);

        $products = $productList->getProducts();
        $this->assertCount(2, $products);
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertInstanceOf(Product::class, $products[1]);

        $arrayRepresentation = $productList->toArray();
        $this->assertCount(2, $arrayRepresentation);
    }

    public function testFromJsonThrowsExceptionWhenProductsKeyMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing or invalid 'products' array.");

        ProductList::fromJson([]);
    }

    public function testFromJsonThrowsExceptionWhenProductsKeyNotArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing or invalid 'products' array.");

        ProductList::fromJson(['products' => 'not-an-array']);
    }

    public function testFromJsonThrowsExceptionWhenZeroProductsGiven(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No products.");

        ProductList::fromJson(['products' => []]);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $products = [
            new Product(1, 3.4, 2.1, 3.0, 4.0),
            new Product(2, 4.9, 1.0, 2.4, 9.9),
        ];

        $productList = new ProductList($products);

        $array = $productList->toArray();

        $this->assertCount(2, $array);

        $this->assertSame([
            'id' => 1,
            'w' => 3.4,
            'h' => 2.1,
            'd' => 3.0,
            'wg' => 4.0,
            'q' => 1,
            'vr' => true,
        ], $array[0]);

        $this->assertSame([
            'id' => 2,
            'w' => 4.9,
            'h' => 1.0,
            'd' => 2.4,
            'wg' => 9.9,
            'q' => 1,
            'vr' => true,
        ], $array[1]);
    }
}
