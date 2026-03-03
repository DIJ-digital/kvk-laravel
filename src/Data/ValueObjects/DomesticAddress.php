<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

readonly class DomesticAddress
{
    public function __construct(
        public string $type,
        public ?string $streetName = null,
        public ?int $houseNumber = null,
        public ?string $houseLetter = null,
        public ?int $poBoxNumber = null,
        public ?string $postalCode = null,
        public ?string $city = null,
    ) {}

    /**
     * @param  array{type: string, straatnaam?: string, huisnummer?: int, huisletter?: string, postbusnummer?: int, postcode?: string, plaats?: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            streetName: $data['straatnaam'] ?? null,
            houseNumber: $data['huisnummer'] ?? null,
            houseLetter: $data['huisletter'] ?? null,
            poBoxNumber: $data['postbusnummer'] ?? null,
            postalCode: $data['postcode'] ?? null,
            city: $data['plaats'] ?? null,
        );
    }
}
