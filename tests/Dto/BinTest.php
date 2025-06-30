<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Packaging;
use App\Packing\Dto\Bin;
use PHPUnit\Framework\TestCase;

class BinTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $bin = new Bin(1, 10.0, 20.0, 30.0, 40.0);

        $this->assertSame(1, $bin->getId());
        $this->assertSame(10.0, $bin->getWidth());
        $this->assertSame(20.0, $bin->getHeight());
        $this->assertSame(30.0, $bin->getLength());
        $this->assertSame(40.0, $bin->getMaxWeight());
    }

    public function testToArray(): void
    {
        $bin = new Bin(2, 5.5, 6.6, 7.7, 8.8);

        $this->assertSame([
            'id' => 2,
            'h' => 6.6,
            'w' => 5.5,
            'd' => 7.7,
            'max_wg' => 8.8,
        ], $bin->toArray());
    }

    public function testFromPackagingCreatesValidBin(): void
    {
        $packaging = $this->createMock(Packaging::class);
        $packaging->method('getId')->willReturn(99);
        $packaging->method('getWidth')->willReturn(1.1);
        $packaging->method('getHeight')->willReturn(2.2);
        $packaging->method('getLength')->willReturn(3.3);
        $packaging->method('getMaxWeight')->willReturn(4.4);

        $bin = Bin::fromPackaging($packaging);

        $this->assertSame(99, $bin->getId());
        $this->assertSame(1.1, $bin->getWidth());
        $this->assertSame(2.2, $bin->getHeight());
        $this->assertSame(3.3, $bin->getLength());
        $this->assertSame(4.4, $bin->getMaxWeight());
    }

    public function testFromPackagingFailsWithInvalidValues(): void
    {
        $packaging = $this->createMock(Packaging::class);
        $packaging->method('getId')->willReturn(0);
        $packaging->method('getWidth')->willReturn(0.0);
        $packaging->method('getHeight')->willReturn(0.0);
        $packaging->method('getLength')->willReturn(0.0);
        $packaging->method('getMaxWeight')->willReturn(0.0);

        $this->expectException(\AssertionError::class);

        Bin::fromPackaging($packaging);
    }
}
