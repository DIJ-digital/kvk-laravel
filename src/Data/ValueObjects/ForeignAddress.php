<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class ForeignAddress implements Arrayable
{
    public function __construct(
        public ?string $streetHouseNumber = null,
        public ?string $postalCodeCity = null,
        public ?string $country = null,
    ) {}

    /**
     * @param  array{straatHuisnummer?: string, postcodeWoonplaats?: string, land?: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            streetHouseNumber: $data['straatHuisnummer'] ?? null,
            postalCodeCity: $data['postcodeWoonplaats'] ?? null,
            country: $data['land'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'straatHuisnummer' => $this->streetHouseNumber,
            'postcodeWoonplaats' => $this->postalCodeCity,
            'land' => $this->country,
        ];
    }
}
