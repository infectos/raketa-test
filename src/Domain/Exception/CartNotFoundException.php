<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Exception;

final class CartNotFoundException extends DomainException
{
    public function __construct(int $customerId)
    {
        parent::__construct("Cart not found for customer ID: {$customerId}");
    }
}
