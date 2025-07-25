<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Redis;

use Psr\Log\LoggerInterface;

final class RedisConnectorFactory
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function create(array $config): RedisConnector
    {
        $host = $config['host'] ?? 'localhost';
        $port = (int)($config['port'] ?? 6379);
        $timeout = (float)($config['timeout'] ?? 2.5);
        $password = $config['password'] ?? null;
        $database = (int)($config['database'] ?? 0);

        return new RedisConnector(
            host: $host,
            port: $port,
            timeout: $timeout,
            password: $password,
            database: $database,
            logger: $this->logger
        );
    }

    public function createFromEnvironment(): RedisConnector
    {
        $config = [
            'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
            'port' => (int)($_ENV['REDIS_PORT'] ?? 6379),
            'timeout' => (float)($_ENV['REDIS_TIMEOUT'] ?? 2.5),
            'password' => $_ENV['REDIS_PASSWORD'] ?? null,
            'database' => (int)($_ENV['REDIS_DATABASE'] ?? 0),
        ];

        return $this->create($config);
    }
}
