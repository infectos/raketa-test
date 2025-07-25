<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain\Entity;

final class Cart
{
    /** @var CartItem[] */
    private  array $items = [];

    public function __construct(
        readonly private string $uuid,
        readonly private Customer $customer,
        readonly private string $paymentMethod,
        readonly private \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @return CartItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        $this->items[$item->getUuid()] = $item;
    }

    public function removeItem(string $itemUuid): void
    {
        unset($this->items[$itemUuid]);
    }
    
    public function getTotalAmount(): int
    {
        return array_reduce(
            $this->items,
            fn(int $total, CartItem $item) => $total + ($item->getTotalPrice()),
            0
        );
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
