<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\UseCase;

use Raketa\BackendTestTask\Application\DTO\AddToCartRequest;
use Raketa\BackendTestTask\Application\Service\CartService;
use Raketa\BackendTestTask\Application\Service\ProductService;
use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Entity\Customer;
use Raketa\BackendTestTask\Domain\Exception\DomainException;
use Raketa\BackendTestTask\Domain\Service\CartService as CartDomainService;

final readonly class AddItemToCartUseCase
{
    public function __construct(
        private CartService $cartService,
        private ProductService $productService,
        private CartDomainService $cartDomainService,
    ) {
    }

    public function execute(AddToCartRequest $request): Cart
    {
        $product = $this->productService->getProduct($request->productUuid);
        
        // Тут кастомер должен браться из аутентификации, а не создаваться
        $customer = new Customer(
            id: $request->customerId,
            firstName: 'test',
            lastName: 'test',
            middleName: '',
            email: 'test@test.test'
        );

        $cart = $this->cartService->getOrCreateCart($customer);

        if (!$this->cartDomainService->canAddItemToCart($cart, $product, $request->quantity)) {
            throw new DomainException('Cannot add this item to cart');
        }

        $cartItem = $this->cartDomainService->createCartItem($product, $request->quantity);
        $cart->addItem($cartItem);

        $this->cartService->saveCart($cart);

        return $cart;
    }
}
