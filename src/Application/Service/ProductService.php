<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Service;

use Raketa\BackendTestTask\Domain\Entity\Product;
use Raketa\BackendTestTask\Domain\Exception\ProductNotFoundException;
use Raketa\BackendTestTask\Domain\Repository\ProductRepositoryInterface;

final readonly class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function getProduct(string $uuid): Product
    {
        $product = $this->productRepository->findByUuid($uuid);
        
        if ($product === null) {
            throw new ProductNotFoundException($uuid);
        }

        return $product;
    }

    /** @return Product[] */
    public function getProductsByCategory(string $category): array
    {
        return $this->productRepository->findActiveByCategory($category);
    }
}
