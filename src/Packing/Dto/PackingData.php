<?php

declare(strict_types=1);

namespace App\Packing\Dto;

class PackingData
{
    public function __construct(
        private readonly ProductList $productList,
        private readonly BinList $binList,
    ) {
    }

    public function getProductList(): ProductList
    {
        return $this->productList;
    }

    public function getBinList(): BinList
    {
        return $this->binList;
    }
}
