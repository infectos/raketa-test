<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Presentation\Serializer;

use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Entity\CartItem;

final class CartSerializer
{
    public function serialize(Cart $cart): array
    {
        return [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => $cart->getCustomer()->getName(),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
            'created_at' => $cart->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'total_amount' => $cart->getTotalAmount(),
            'items_count' => count($cart->getItems()),
            'is_empty' => $cart->isEmpty(),
            'items' => array_map(
                fn(CartItem $item): array => $this->serializeCartItem($item),
                $cart->getItems()
            ),
        ];
    }

    public function serializeCartItem(CartItem $item): array
    {
        return [
            'uuid' => $item->getUuid(),
            'product_uuid' => $item->getProductUuid(),
            'price' => $item->getPrice(),
            'quantity' => $item->getQuantity(),
            'total_price' => $item->getTotalPrice(),
        ];
    }

    public function serializeCollection(array $carts): array
    {
        return array_map(
            fn(Cart $cart): array => $this->serialize($cart),
            $carts
        );
    }

    public function serializeSummary(Cart $cart): array
    {
        return [
            'uuid' => $cart->getUuid(),
            'customer_id' => $cart->getCustomer()->getId(),
            'total_amount' => $cart->getTotalAmount(),
            'items_count' => count($cart->getItems()),
            'is_empty' => $cart->isEmpty(),
            'created_at' => $cart->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
