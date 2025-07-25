<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Presentation\Serializer;

use Raketa\BackendTestTask\Domain\Entity\Product;

final class ProductSerializer
{
    public function serialize(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'category' => $product->getCategory(),
            'price' => $product->getPrice(),
            'thumbnail' => $product->getThumbnail(),
            'is_active' => $product->isActive(),
        ];
    }

    public function serializeCollection(array $products): array
    {
        return array_map(
            fn(Product $product): array => $this->serialize($product),
            $products
        );
    }

    public function serializeSummary(Product $product): array
    {
        return [
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            'category' => $product->getCategory(),
            'price' => $product->getPrice(),
            'thumbnail' => $product->getThumbnail(),
        ];
    }

    public function serializeForCart(Product $product): array
    {
        return [
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'thumbnail' => $product->getThumbnail(),
        ];
    }
}
