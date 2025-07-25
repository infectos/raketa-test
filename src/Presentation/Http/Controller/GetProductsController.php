<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Presentation\Http\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Application\UseCase\GetProductsByCategoryUseCase;
use Raketa\BackendTestTask\Application\DTO\GetProductsRequest;
use Raketa\BackendTestTask\Infrastructure\Http\Response\JsonResponse;
use Raketa\BackendTestTask\Presentation\Serializer\ProductSerializer;

final readonly class GetProductsController
{
    public function __construct(
        private GetProductsByCategoryUseCase $getProductsByCategoryUseCase,
        private ProductSerializer $productSerializer,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            $queryParams = [];
            parse_str($request->getUri()->getQuery(), $queryParams);
            
            $dto = new GetProductsRequest(
                category: $queryParams['category'] ?? ''
            );

            $products = $this->getProductsByCategoryUseCase->execute($dto);

            return JsonResponse::success([
                'products' => $this->productSerializer->serializeCollection($products)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Unexpected error in GetProducts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return JsonResponse::internalServerError(['error' => 'Internal server error']);
        }
    }
}
