<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class DomesticAddress implements Arrayable
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'straatnaam' => $this->streetName,
            'huisnummer' => $this->houseNumber,
            'huisletter' => $this->houseLetter,
            'postbusnummer' => $this->poBoxNumber,
            'postcode' => $this->postalCode,
            'plaats' => $this->city,
        ];
    }
}
