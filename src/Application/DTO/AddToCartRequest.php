<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\DTO;

final readonly class AddToCartRequest
{
    public function __construct(
        public string $productUuid,
        public int $quantity,
        public int $customerId,
    ) {
    }
}
