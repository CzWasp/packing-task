<?php

declare(strict_types=1);

namespace App\Tests\Dto;

use App\Packing\Dto\Product;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $product = new Product(1, 3.4, 2.1, 3.0, 4.0, 2, false);

        $this->assertSame(1, $product->getId());
        $this->assertSame(3.4, $product->getWidth());
        $this->assertSame(2.1, $product->getHeight());
        $this->assertSame(3.0, $product->getLength());
        $this->assertSame(4.0, $product->getWeight());
        $this->assertSame(2, $product->getQuantity());
        $this->assertFalse($product->isVerticalRotation());
    }

    public function testFromJsonCreatesProduct(): void
    {
        $jsonData = [
            'id' => 1,
            'width' => 3.4,
            'height' => 2.1,
            'length' => 3.0,
            'weight' => 4.0,
        ];

        $product = Product::fromJson($jsonData);

        $this->assertSame(1, $product->getId());
        $this->assertSame(3.4, $product->getWidth());
        $this->assertSame(2.1, $product->getHeight());
        $this->assertSame(3.0, $product->getLength());
        $this->assertSame(4.0, $product->getWeight());
        $this->assertSame(1, $product->getQuantity());
        $this->assertTrue($product->isVerticalRotation());
    }

    public function testFromJsonThrowsExceptionWhenKeyMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key: weight');

        $invalidData = [
            'id' => 1,
            'width' => 3.4,
            'height' => 2.1,
            'length' => 3.0,
        ];

        Product::fromJson($invalidData);
    }

    public function testFromJsonThrowsExceptionWhenValuesNotNumeric(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dimensions and weight must be numeric.');

        $invalidData = [
            'id' => 1,
            'width' => 'foo',
            'height' => 2.1,
            'length' => 3.0,
            'weight' => 4.0,
        ];

        Product::fromJson($invalidData);
    }

    public function testToArray(): void
    {
        $product = new Product(5, 10.1, 20.2, 30.3, 40.4, 3, true);

        $expected = [
            'id' => 5,
            'w' => 10.1,
            'h' => 20.2,
            'd' => 30.3,
            'wg' => 40.4,
            'q' => 3,
            'vr' => true,
        ];

        $this->assertSame($expected, $product->toArray());
    }
}
