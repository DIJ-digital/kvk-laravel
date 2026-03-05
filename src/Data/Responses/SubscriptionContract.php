<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SubscriptionContract implements Arrayable
{
    public function __construct(
        public string $id,
    ) {}

    /**
     * @param  array{id: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }

    public static function fake(string $id = 'contract-789'): self
    {
        return new self(id: $id);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
