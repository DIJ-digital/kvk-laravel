<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

readonly class TradeName
{
    public function __construct(
        public string $name,
        public int $order,
    ) {}

    /**
     * @param  array{naam: string, volgorde: int}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['naam'],
            order: $data['volgorde'],
        );
    }

    public static function fake(
        string $name = 'Test BV Donald',
        int $order = 1,
    ): self {
        return new self(
            name: $name,
            order: $order,
        );
    }
}
