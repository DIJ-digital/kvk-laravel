<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class TradeName implements Arrayable
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'naam' => $this->name,
            'volgorde' => $this->order,
        ];
    }
}
