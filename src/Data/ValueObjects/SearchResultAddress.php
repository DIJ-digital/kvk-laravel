<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SearchResultAddress implements Arrayable
{
    public function __construct(
        public ?DomesticAddress $domesticAddress = null,
        public ?ForeignAddress $foreignAddress = null,
    ) {}

    /**
     * @param  array{binnenlandsAdres?: array{type: string, straatnaam?: string, huisnummer?: int, huisletter?: string, postbusnummer?: int, postcode?: string, plaats?: string}, buitenlandsAdres?: array{straatHuisnummer?: string, postcodeWoonplaats?: string, land?: string}}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            domesticAddress: isset($data['binnenlandsAdres'])
                ? DomesticAddress::fromArray($data['binnenlandsAdres'])
                : null,
            foreignAddress: isset($data['buitenlandsAdres'])
                ? ForeignAddress::fromArray($data['buitenlandsAdres'])
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'binnenlandsAdres' => $this->domesticAddress?->toArray(),
            'buitenlandsAdres' => $this->foreignAddress?->toArray(),
        ];
    }
}
