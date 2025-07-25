<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\UseCase;

use Raketa\BackendTestTask\Application\Service\CartService;
use Raketa\BackendTestTask\Domain\Entity\Cart;

final readonly class GetCartUseCase
{
    public function __construct(
        private CartService $cartService,
    ) {
    }

    public function execute(int $customerId): Cart
    {
        return $this->cartService->getCart($customerId);
    }
}
