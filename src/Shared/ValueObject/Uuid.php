<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Shared\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;

final readonly class Uuid
{
    private function __construct(
        private UuidInterface $uuid
    ) {
    }

    public static function fromString(string $value): self
    {
        $uuid = RamseyUuid::fromString($value);
        return new self($uuid);
    }

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4());
    }

    public static function generateV1(): self
    {
        return new self(RamseyUuid::uuid1());
    }

    public static function generateV6(): self
    {
        return new self(RamseyUuid::uuid6());
    }

    public static function generateV7(): self
    {
        return new self(RamseyUuid::uuid7());
    }

    public function getValue(): string
    {
        return $this->uuid->toString();
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function equals(self $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }

    public function getVersion(): int
    {
        return $this->uuid->getVersion();
    }

    public function getVariant(): int
    {
        return $this->uuid->getVariant();
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        if (method_exists($this->uuid, 'getDateTime')) {
            return $this->uuid->getDateTime();
        }
        
        return null;
    }

    public static function isValidFormat(string $uuid): bool
    {
        return RamseyUuid::isValid($uuid);
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid->toString(),
            'version' => $this->uuid->getVersion(),
            'variant' => $this->uuid->getVariant(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return self::fromString($data['uuid']);
    }

    public function getBytes(): string
    {
        return $this->uuid->getBytes();
    }

    public function getHex(): string
    {
        return $this->uuid->getHex()->toString();
    }
}
