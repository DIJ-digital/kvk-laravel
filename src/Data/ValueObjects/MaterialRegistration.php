<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

readonly class MaterialRegistration
{
    public function __construct(
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}

    /**
     * @param  array{datumAanvang?: string, datumEinde?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            startDate: $data['datumAanvang'] ?? null,
            endDate: $data['datumEinde'] ?? null,
        );
    }

    public static function fake(
        ?string $startDate = '20150101',
        ?string $endDate = null,
    ): self {
        return new self(
            startDate: $startDate,
            endDate: $endDate,
        );
    }
}
