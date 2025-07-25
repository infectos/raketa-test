<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Service;

use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Entity\CartItem;
use Raketa\BackendTestTask\Domain\Entity\Customer;
use Raketa\BackendTestTask\Domain\Entity\Product;
use Raketa\BackendTestTask\Shared\ValueObject\Uuid;

final class CartService
{
    public function createCart(Customer $customer): Cart
    {
        return new Cart(
            uuid: Uuid::generate(),
            customer: $customer,
            paymentMethod: 'card',
            createdAt: new \DateTimeImmutable()
        );
    }

    public function createCartItem(Product $product, int $quantity): CartItem
    {
        return new CartItem(
            uuid: Uuid::generate(),
            productUuid: $product->getUuid(),
            price: $product->getPrice(),
            quantity: $quantity
        );
    }

    public function canAddItemToCart(Cart $cart, Product $product, int $quantity): bool
    {
        if (!$product->isActive()) {
            return false;
        }

        if ($quantity <= 0) {
            return false;
        }

        return true;
    }
}
