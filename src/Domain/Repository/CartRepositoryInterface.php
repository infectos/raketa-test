<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Repository;

use Raketa\BackendTestTask\Domain\Entity\Cart;

interface CartRepositoryInterface
{
    public function save(Cart $cart): void;
    public function findByCustomerId(int $customerId): ?Cart;
    public function delete(string $cartUuid): void;
}
