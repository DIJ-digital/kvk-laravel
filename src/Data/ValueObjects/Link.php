<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class Link implements Arrayable
{
    public function __construct(
        public string $rel,
        public string $href,
    ) {}

    /**
     * @param  array{rel: string, href: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rel: $data['rel'],
            href: $data['href'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'rel' => $this->rel,
            'href' => $this->href,
        ];
    }
}
