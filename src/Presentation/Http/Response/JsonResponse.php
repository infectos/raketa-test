<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Presentation\Http\Response;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Класс заглушка
 */
final class JsonResponse implements ResponseInterface
{

    private int $statusCode = 200;
    private string $reasonPhrase = 'OK';
    private StreamInterface $body;

    private function __construct(array $data, int $statusCode, string $reasonPhrase)
    {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->body = new SomeClassImplementingStreamInterface(json_encode($data));
    }

    public static function success(array $data): self
    {
        return new self($data, 200, 'OK');
    }

    public static function badRequest(array $data): self
    {
        return new self($data, 400, 'Bad Request');
    }

    public static function notFound(array $data): self
    {
        return new self($data, 404, 'Not Found');
    }

    public static function internalServerError(array $data): self
    {
        return new self($data, 500, 'Internal Server Error');
    }

    public function getProtocolVersion(): string
    {
        // TODO: Implement getProtocolVersion() method.
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        // TODO: Implement withProtocolVersion() method.
    }

    public function getHeaders(): array
    {
        // TODO: Implement getHeaders() method.
    }

    public function hasHeader(string $name): bool
    {
        // TODO: Implement hasHeader() method.
    }

    public function getHeader(string $name): array
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaderLine(string $name): string
    {
        // TODO: Implement getHeaderLine() method.
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader(string $name): MessageInterface
    {
        // TODO: Implement withoutHeader() method.
    }

    public function getBody(): StreamInterface
    {
        // TODO: Implement getBody() method.
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        // TODO: Implement withBody() method.
    }

    public function getStatusCode(): int
    {
        // TODO: Implement getStatusCode() method.
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        // TODO: Implement withStatus() method.
    }

    public function getReasonPhrase(): string
    {
        // TODO: Implement getReasonPhrase() method.
    }
}
