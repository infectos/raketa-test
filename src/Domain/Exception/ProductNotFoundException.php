<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Exception;

final class ProductNotFoundException extends DomainException
{
    public function __construct(int $productUuid)
    {
        parent::__construct("Product not found with UUID: {$productUuid}");
    }
}
