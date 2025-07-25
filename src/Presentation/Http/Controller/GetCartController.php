<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Presentation\Http\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Application\UseCase\GetCartUseCase;
use Raketa\BackendTestTask\Infrastructure\Http\Response\JsonResponse;
use Raketa\BackendTestTask\Presentation\Serializer\CartSerializer;
use Raketa\BackendTestTask\Domain\Exception\CartNotFoundException;

final readonly class GetCartController
{
    public function __construct(
        private GetCartUseCase $getCartUseCase,
        private CartSerializer $cartSerializer,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            //Должен быть из аутентификации, а не 1
            $customerId = 1;
            
            $cart = $this->getCartUseCase->execute($customerId);

            return JsonResponse::success(
                $this->cartSerializer->serialize($cart)
            );

        } catch (CartNotFoundException $e) {
            return JsonResponse::notFound(['error' => 'Cart not found']);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error in GetCart', ['error' => $e->getMessage()]);
            return JsonResponse::internalServerError(['error' => 'Internal server error']);
        }
    }
}
