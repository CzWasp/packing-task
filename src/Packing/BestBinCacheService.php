<?php

declare(strict_types=1);

namespace App\Packing;

use App\Packing\Dto\Product;
use App\Packing\Dto\ProductList;

class BestBinCacheService
{
    public function getCacheKey(ProductList $productList): string
    {
        $normalizedProducts = $this->orderProductsByLengthSide($productList->getProducts());

        return md5(json_encode($normalizedProducts));
    }

    /**
     * @param Product[] $products
     * @return list<array<string, float|int>>
     */
    private function orderProductsByLengthSide(array $products): array
    {
        $normalizedProducts = array_map(function (Product $product) {
            $sideLengths = [
                $product->getLength(),
                $product->getHeight(),
                $product->getWidth(),
            ];
            sort($sideLengths);
            return [
                'side1' => round($sideLengths[0], 2),
                'side2' => round($sideLengths[1], 2),
                'side3' => round($sideLengths[2], 2),
                'weight' => round($product->getWeight(), 2),
                'qty' => $product->getQuantity(),
            ];
        }, $products);

        usort($normalizedProducts, function (array $a, array $b) {
            return
                $b['side1'] <=> $a['side1'] ?:
                $b['side2'] <=> $a['side2'] ?:
                $b['side3'] <=> $a['side3'];
        });

        return $normalizedProducts;
    }
}
