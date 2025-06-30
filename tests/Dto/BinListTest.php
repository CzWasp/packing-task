<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Packaging;
use App\Packing\Dto\Bin;
use App\Packing\Dto\BinList;
use PHPUnit\Framework\TestCase;

class BinListTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $bin1 = new Bin(1, 10.0, 20.0, 30.0, 40.0);
        $bin2 = new Bin(2, 5.5, 6.6, 7.7, 8.8);

        $binList = new BinList([$bin1, $bin2]);

        $bins = $binList->getBins();
        $this->assertCount(2, $bins);
        $this->assertSame($bin1, $bins[0]);
        $this->assertSame($bin2, $bins[1]);
    }

    public function testToArray(): void
    {
        $bin1 = new Bin(1, 10.0, 20.0, 30.0, 40.0);
        $bin2 = new Bin(2, 5.5, 6.6, 7.7, 8.8);

        $binList = new BinList([$bin1, $bin2]);

        $expected = [
            $bin1->toArray(),
            $bin2->toArray(),
        ];

        $this->assertSame($expected, $binList->toArray());
    }

    public function testFromPackagingListCreatesBins(): void
    {
        $packaging1 = $this->createMock(Packaging::class);
        $packaging1->method('getId')->willReturn(11);
        $packaging1->method('getWidth')->willReturn(1.1);
        $packaging1->method('getHeight')->willReturn(2.2);
        $packaging1->method('getLength')->willReturn(3.3);
        $packaging1->method('getMaxWeight')->willReturn(4.4);

        $packaging2 = $this->createMock(Packaging::class);
        $packaging2->method('getId')->willReturn(22);
        $packaging2->method('getWidth')->willReturn(5.5);
        $packaging2->method('getHeight')->willReturn(6.6);
        $packaging2->method('getLength')->willReturn(7.7);
        $packaging2->method('getMaxWeight')->willReturn(8.8);

        $binList = BinList::fromPackagingList([$packaging1, $packaging2]);

        $bins = $binList->getBins();

        $this->assertCount(2, $bins);
        $this->assertSame(11, $bins[0]->getId());
        $this->assertSame(5.5, $bins[1]->getWidth());
    }

    public function testFromPackagingListThrowsAssertionErrorOnEmpty(): void
    {
        $this->expectException(\AssertionError::class);

        BinList::fromPackagingList([]);
    }
}
