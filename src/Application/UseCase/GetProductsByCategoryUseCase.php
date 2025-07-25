<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\UseCase;

use Raketa\BackendTestTask\Application\DTO\GetProductsRequest;
use Raketa\BackendTestTask\Application\Service\ProductService;

final readonly class GetProductsByCategoryUseCase
{
    public function __construct(
        private ProductService $productService,
    ) {
    }

    /** @return \Raketa\BackendTestTask\Domain\Entity\Product[] */
    public function execute(GetProductsRequest $request): array
    {
        return $this->productService->getProductsByCategory($request->category);
    }
}
