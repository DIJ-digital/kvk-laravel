<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

readonly class Link
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
}
