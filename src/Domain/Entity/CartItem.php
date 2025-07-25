<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain\Entity;

final readonly class CartItem
{
    public function __construct(
        private string $uuid,
        private string $productUuid,
        private int $price,
        private int $quantity,
    ) {
        if ($this->quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be greater than 0");
        }
        if ($this->price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative");
        }
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getProductUuid(): string
    {
        return $this->productUuid;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): int
    {
        return $this->price * $this->quantity;
    }
}
