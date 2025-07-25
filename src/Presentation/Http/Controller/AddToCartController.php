<?php

namespace Raketa\BackendTestTask\Presentation\Http\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Application\UseCase\AddItemToCartUseCase;
use Raketa\BackendTestTask\Application\DTO\AddToCartRequest;
use Raketa\BackendTestTask\Infrastructure\Http\Response\JsonResponse;
use Raketa\BackendTestTask\Presentation\Serializer\CartSerializer;
use Raketa\BackendTestTask\Domain\Exception\ProductNotFoundException;

final readonly class AddToCartController
{
    public function __construct(
        private AddItemToCartUseCase $addItemToCartUseCase,
        private CartSerializer $cartSerializer,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            $rawRequest = json_decode($request->getBody()->getContents(), true);
            
            $dto = new AddToCartRequest(
                productUuid: $rawRequest['productUuid'] ?? '',
                quantity: (int) $rawRequest['quantity'] ?? 1,
                customerId: (int) $rawRequest['customerId'] ?? 0,
            );
            $cart = $this->addItemToCartUseCase->execute($dto);
            
            return JsonResponse::success([
                'message' => 'Item added to cart successfully',
                'cart' => $this->cartSerializer->serialize($cart)
            ]);
        } catch (ProductNotFoundException $e) {
            $this->logger->warning('Product not found', ['error' => $e->getMessage()]);
            return JsonResponse::notFound(['error' => 'Product not found']);
        } catch (\Exception $e) {

            $this->logger->error('Unexpected error in AddToCart', ['error' => $e->getMessage()]);
            return JsonResponse::internalServerError(['error' => 'Internal server error']);
        }
    }
}
