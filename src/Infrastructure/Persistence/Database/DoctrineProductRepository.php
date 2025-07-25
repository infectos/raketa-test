<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Database;

use Doctrine\DBAL\Connection;
use Raketa\BackendTestTask\Domain\Entity\Product;
use Raketa\BackendTestTask\Domain\Repository\ProductRepositoryInterface;

final class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function findByUuid(string $uuid): ?Product
    {
        $qb = $this->connection->createQueryBuilder();
        
        $row = $qb
            ->select('*')
            ->from('products')
            ->where('uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            return null;
        }

        return $this->createProductFromRow($row);
    }

    public function findByCategory(string $category): array
    {
        $qb = $this->connection->createQueryBuilder();
        
        $rows = $qb
            ->select('*')
            ->from('products')
            ->where('category = :category')
            ->andWhere('is_active = 1')
            ->setParameter('category', $category)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(
            fn(array $row): Product => $this->createProductFromRow($row),
            $rows
        );
    }

    private function createProductFromRow(array $row): Product
    {
        return new Product(
            uuid: $row['uuid'],
            isActive: (bool)$row['is_active'],
            category: $row['category'],
            name: $row['name'],
            description: $row['description'] ?? '',
            thumbnail: $row['thumbnail'] ?? '',
            price: (int)$row['price'],
        );
    }
}
