<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain\Entity;

final readonly class Product
{
    public function __construct(
        private string $uuid,
        private bool $isActive,
        private string $category,
        private string $name,
        private string $description,
        private string $thumbnail,
        private int $price,
    ) {
        if ($this->price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative");
        }
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
