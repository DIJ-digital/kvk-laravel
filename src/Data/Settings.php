<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class Settings implements Arrayable
{
    public function __construct(
        public string $base_url,
        public string $api_key,
    ) {}

    /**
     * @param  array<string, string>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            base_url: $data['base_url'],
            api_key: $data['api_key'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'base_url' => $this->base_url,
            'api_key' => $this->api_key,
        ];
    }
}
