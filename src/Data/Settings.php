<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data;

readonly class Settings
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
}
