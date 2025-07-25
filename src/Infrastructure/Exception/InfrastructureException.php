<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Exception;

class InfrastructureException extends \Exception
{
    public function __construct(
        string $message = 'Infrastructure error',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
