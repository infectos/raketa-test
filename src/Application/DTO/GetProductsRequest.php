<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\DTO;

final readonly class GetProductsRequest
{
    public function __construct(
        public string $category,
    ) {
    }
}
