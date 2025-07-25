<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Service;

use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Entity\Customer;
use Raketa\BackendTestTask\Domain\Exception\CartNotFoundException;
use Raketa\BackendTestTask\Domain\Repository\CartRepositoryInterface;
use Raketa\BackendTestTask\Domain\Service\CartService as CartDomainService;

final readonly class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private CartDomainService $cartDomainService,
    ) {
    }

    public function getOrCreateCart(Customer $customer): Cart
    {
        $cart = $this->cartRepository->findByCustomerId($customer->getId());
        
        if ($cart === null) {
            $cart = $this->cartDomainService->createCart($customer);
            $this->cartRepository->save($cart);
        }

        return $cart;
    }

    public function saveCart(Cart $cart): void
    {
        $this->cartRepository->save($cart);
    }

    public function getCart(int $customerId): Cart
    {
        $cart = $this->cartRepository->findByCustomerId($customerId);
        
        if ($cart === null) {
            throw new CartNotFoundException($customerId);
        }

        return $cart;
    }

    public function clearCart(int $customerId): void
    {
        $cart = $this->getCart($customerId);
        $cart->clear();
        $this->cartRepository->save($cart);
    }
}
