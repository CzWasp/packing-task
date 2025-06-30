<?php

declare(strict_types=1);

namespace App;

use App\Packing\PackingApiCalculator;
use App\Packing\PackingApiClient;
use App\Packing\Dto\BestBox;
use App\Packing\Dto\PackingData;
use App\Packing\Dto\ProductList;
use App\Packing\Dto\BinList;
use App\Packing\Dto\Product;
use App\Packing\Dto\Bin;
use App\Packing\PackingApiException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class PackingApiCalculatorTest extends TestCase
{
    private PackingApiClient&MockObject $client;
    private PackingApiCalculator $calculator;

    protected function setUp(): void
    {
        $this->client = $this->createMock(PackingApiClient::class);
        $this->calculator = new PackingApiCalculator($this->client);
    }

    public function testReturnsBestBox(): void
    {
        $packingData = $this->createPackingData();

        $this->client->method('fetchPackingResult')->willReturn([
            'response' => [
                'status' => 1,
                'bins_packed' => [
                    ['bin_data' => ['id' => 42]],
                ],
            ],
        ]);

        $box = $this->calculator->getBestBoxForPackingData($packingData);

        $this->assertInstanceOf(BestBox::class, $box);
        $this->assertSame(42, $box->getId());
    }

    public function testThrowsIfResponseMissing(): void
    {
        $this->client->method('fetchPackingResult')->willReturn([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing 'response' in API result.");

        $this->calculator->getBestBoxForPackingData($this->createPackingData());
    }

    public function testThrowsIfStatusIsNotOne(): void
    {
        $this->client->method('fetchPackingResult')->willReturn([
            'response' => [
                'status' => 0,
                'errors' => [],
            ],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("API response indicates failure.");

        $this->calculator->getBestBoxForPackingData($this->createPackingData());
    }

    public function testThrowsIfErrorArrayPresent(): void
    {
        $this->client->method('fetchPackingResult')->willReturn([
            'response' => [
                'status' => 0,
                'errors' => [
                    ['level' => 'critical', 'message' => 'Invalid input']
                ],
            ],
        ]);

        $this->expectException(PackingApiException::class);
        $this->expectExceptionMessage('Invalid input');

        $this->calculator->getBestBoxForPackingData($this->createPackingData());
    }

    public function testThrowsIfNoBinsPacked(): void
    {
        $this->client->method('fetchPackingResult')->willReturn([
            'response' => [
                'status' => 1,
                'bins_packed' => [],
            ],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No packed bins returned.");

        $this->calculator->getBestBoxForPackingData($this->createPackingData());
    }

    public function testThrowsIfMoreThanOneBinPacked(): void
    {
        $this->client->method('fetchPackingResult')->willReturn([
            'response' => [
                'status' => 1,
                'bins_packed' => [
                    ['bin_data' => ['id' => 1]],
                    ['bin_data' => ['id' => 2]],
                ],
            ],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot pack into more than one bin.");

        $this->calculator->getBestBoxForPackingData($this->createPackingData());
    }

    private function createPackingData(): PackingData
    {
        return new PackingData(
            new ProductList([
                new Product(1, 5.0, 5.0, 5.0, 1.0, 1),
            ]),
            new BinList([
                new Bin(10, 10.0, 10.0, 10.0, 10.0),
            ])
        );
    }
}
