<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Redis;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Repository\CartRepositoryInterface;
use Raketa\BackendTestTask\Infrastructure\Exception\InfrastructureException;

final class RedisCartRepository implements CartRepositoryInterface
{
    private const CART_KEY_PREFIX = 'cart:customer:';
    private const CART_UUID_KEY_PREFIX = 'cart:uuid:';
    private const CART_TTL = 86400; // 24 hours

    public function __construct(
        private readonly RedisConnector $redisConnector,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function save(Cart $cart): void
    {
        try {
            $customerKey = $this->getCartKeyByCustomerId($cart->getCustomer()->getId());
            $uuidKey = $this->getCartKeyByUuid($cart->getUuid());
            $serializedCart = serialize($cart);

            $this->redisConnector->setex($customerKey, self::CART_TTL, $serializedCart);

            $this->redisConnector->setex(
                $uuidKey, 
                self::CART_TTL, 
                (string)$cart->getCustomer()->getId()
            );

        } catch (\Exception $e) {
            $this->logger->error('Failed to save cart', [
                'customer_id' => $cart->getCustomer()->getId(),
                'cart_uuid' => $cart->getUuid(),
                'error' => $e->getMessage()
            ]);
            throw new InfrastructureException('Failed to save cart', 0, $e);
        }
    }

    public function findByCustomerId(int $customerId): ?Cart
    {
        try {
            $key = $this->getCartKeyByCustomerId($customerId);
            $serializedCart = $this->redisConnector->get($key);
            
            if ($serializedCart === false) {
                return null;
            }

            $cart = unserialize($serializedCart);
            
            if (!$cart instanceof Cart) {
                $this->logger->warning('Invalid cart data in Redis', ['customer_id' => $customerId]);
                $this->redisConnector->del($key); // Clean up invalid data
                return null;
            }

            return $cart;
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve cart', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function findByUuid(string $cartUuid): ?Cart
    {
        try {
            $uuidKey = $this->getCartKeyByUuid($cartUuid);
            $customerIdStr = $this->redisConnector->get($uuidKey);

            if ($customerIdStr === false) {
                return null;
            }

            $customerId = (int)$customerIdStr;

            $cart = $this->findByCustomerId($customerId);

            if ($cart && $cart->getUuid() !== $cartUuid) {
                $this->logger->warning('Cart UUID mismatch', [
                    'expected_uuid' => $cartUuid,
                    'actual_uuid' => $cart->getUuid(),
                    'customer_id' => $customerId
                ]);
                return null;
            }

            return $cart;
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve cart by UUID', [
                'cart_uuid' => $cartUuid,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function delete(string $cartUuid): void
    {
        try {
            $uuidKey = $this->getCartKeyByUuid($cartUuid);
            $customerIdStr = $this->redisConnector->get($uuidKey);
            
            if ($customerIdStr === false) {
                $this->logger->info('Cart UUID not found for deletion', ['cart_uuid' => $cartUuid]);
                return;
            }

            $customerId = (int)$customerIdStr;
            $customerKey = $this->getCartKeyByCustomerId($customerId);
            
            $existingCart = $this->findByCustomerId($customerId);
            if ($existingCart && $existingCart->getUuid() === $cartUuid) {
                $deletedCount = $this->redisConnector->del($customerKey, $uuidKey);

                $this->logger->info('Cart deleted successfully', [
                    'cart_uuid' => $cartUuid,
                    'customer_id' => $customerId,
                    'keys_deleted' => $deletedCount
                ]);
            } else {
                $this->logger->warning('Cart UUID mismatch during deletion', [
                    'cart_uuid' => $cartUuid,
                    'customer_id' => $customerId,
                    'existing_cart_uuid' => $existingCart?->getUuid()
                ]);

                $this->redisConnector->del($uuidKey);
            }

        } catch (\Exception $e) {
            $this->logger->error('Failed to delete cart', [
                'cart_uuid' => $cartUuid,
                'error' => $e->getMessage()
            ]);
            throw new InfrastructureException('Failed to delete cart', 0, $e);
        }
    }

    public function deleteByCustomerId(int $customerId): void
    {
        try {
            $cart = $this->findByCustomerId($customerId);
            
            if (!$cart) {
                $this->logger->info('No cart found for customer deletion', ['customer_id' => $customerId]);
                return;
            }

            $customerKey = $this->getCartKeyByCustomerId($customerId);
            $uuidKey = $this->getCartKeyByUuid($cart->getUuid());
            
            $deletedCount = $this->redisConnector->del($customerKey, $uuidKey);
            
            $this->logger->info('Cart deleted by customer ID', [
                'customer_id' => $customerId,
                'cart_uuid' => $cart->getUuid(),
                'keys_deleted' => $deletedCount
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to delete cart by customer ID', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            throw new InfrastructureException('Failed to delete cart by customer ID', 0, $e);
        }
    }

    private function getCartKeyByCustomerId(int $customerId): string
    {
        return self::CART_KEY_PREFIX . $customerId;
    }

    private function getCartKeyByUuid(string $cartUuid): string
    {
        return self::CART_UUID_KEY_PREFIX . $cartUuid;
    }
}
