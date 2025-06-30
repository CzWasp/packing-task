<?php

declare(strict_types=1);

namespace App\Packing\Dto;

use InvalidArgumentException;

class ProductList
{
    /**
     * @param Product[] $products
     */
    public function __construct(
        private readonly array $products
    ) {
    }

    /**
     * @param mixed[] $input
     */
    public static function fromJson(array $input): self
    {
        if (!isset($input['products']) || !is_array($input['products'])) {
            throw new InvalidArgumentException("Missing or invalid 'products' array.");
        }

        if (count($input['products']) === 0) {
            throw new InvalidArgumentException("No products.");
        }

        if (count($input['products']) >= 5000) {
            throw new InvalidArgumentException("Too many products.");
        }

        $products = [];
        foreach ($input['products'] as $productData) {
            $products[] = Product::fromJson($productData);
        }

        return new self($products);
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return array<int, array<string, int|float|bool>>
     */
    public function toArray(): array
    {
        return array_map(fn(Product $product) => $product->toArray(), $this->products);
    }
}
