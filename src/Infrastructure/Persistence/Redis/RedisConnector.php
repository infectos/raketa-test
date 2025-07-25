<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Redis;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Infrastructure\Exception\InfrastructureException;

final class RedisConnector
{
    private ?\Redis $connection = null;
    private bool $isConnected = false;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly float $timeout,
        private readonly ?string $password,
        private readonly int $database,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function connect(): void
    {
        if ($this->isConnected) {
            return;
        }

        try {
            $this->connection = new \Redis();
            
            if (!$this->connection->connect($this->host, $this->port, $this->timeout)) {
                throw new InfrastructureException(
                    sprintf('Failed to connect to Redis at %s:%d', $this->host, $this->port)
                );
            }

            if ($this->password !== null) {
                if (!$this->connection->auth($this->password)) {
                    throw new InfrastructureException('Redis authentication failed');
                }
            }

            if (!$this->connection->select($this->database)) {
                throw new InfrastructureException(
                    sprintf('Failed to select Redis database %d', $this->database)
                );
            }

            $this->isConnected = true;
            
            $this->logger->info('Redis connection established', [
                'host' => $this->host,
                'port' => $this->port,
                'database' => $this->database
            ]);

        } catch (\RedisException $e) {
            $this->logger->error('Redis connection failed', [
                'host' => $this->host,
                'port' => $this->port,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException(
                'Redis connection failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function disconnect(): void
    {
        if ($this->connection && $this->isConnected) {
            $this->connection->close();
            $this->isConnected = false;
            $this->connection = null;
            
            $this->logger->info('Redis connection closed');
        }
    }

    public function isConnected(): bool
    {
        if (!$this->isConnected || !$this->connection) {
            return false;
        }

        try {
            return $this->connection->ping() === '+PONG';
        } catch (\RedisException) {
            $this->isConnected = false;
            return false;
        }
    }

    public function get(string $key): string|false
    {
        $this->ensureConnected();
        
        try {
            return $this->connection->get($key);
        } catch (\RedisException $e) {
            $this->logger->error('Redis GET operation failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException('Redis GET operation failed', 0, $e);
        }
    }

    public function set(string $key, string $value): bool
    {
        $this->ensureConnected();
        
        try {
            return $this->connection->set($key, $value);
        } catch (\RedisException $e) {
            $this->logger->error('Redis SET operation failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException('Redis SET operation failed', 0, $e);
        }
    }

    public function setex(string $key, int $ttl, string $value): bool
    {
        $this->ensureConnected();
        
        try {
            return $this->connection->setex($key, $ttl, $value);
        } catch (\RedisException $e) {
            $this->logger->error('Redis SETEX operation failed', [
                'key' => $key,
                'ttl' => $ttl,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException('Redis SETEX operation failed', 0, $e);
        }
    }

    public function del(string $key): int
    {
        $this->ensureConnected();
        
        try {
            return $this->connection->del($key);
        } catch (\RedisException $e) {
            $this->logger->error('Redis DEL operation failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException('Redis DEL operation failed', 0, $e);
        }
    }

    public function exists(string $key): bool
    {
        $this->ensureConnected();
        
        try {
            return $this->connection->exists($key) > 0;
        } catch (\RedisException $e) {
            $this->logger->error('Redis EXISTS operation failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException('Redis EXISTS operation failed', 0, $e);
        }
    }

    public function expire(string $key, int $ttl): bool
    {
        $this->ensureConnected();
        
        try {
            return $this->connection->expire($key, $ttl);
        } catch (\RedisException $e) {
            $this->logger->error('Redis EXPIRE operation failed', [
                'key' => $key,
                'ttl' => $ttl,
                'error' => $e->getMessage()
            ]);
            
            throw new InfrastructureException('Redis EXPIRE operation failed', 0, $e);
        }
    }

    private function ensureConnected(): void
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
