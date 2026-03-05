<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class Address implements Arrayable
{
    public function __construct(
        public string $type,
        public ?string $shielded = null,
        public ?string $fullAddress = null,
        public ?string $streetName = null,
        public ?int $houseNumber = null,
        public ?string $houseNumberAddition = null,
        public ?string $houseLetter = null,
        public ?string $addressAddition = null,
        public ?string $postalCode = null,
        public ?int $poBoxNumber = null,
        public ?string $city = null,
        public ?string $streetHouseNumber = null,
        public ?string $postalCodeCity = null,
        public ?string $region = null,
        public ?string $country = null,
        public ?GeoData $geoData = null,
    ) {}

    /**
     * @param  array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            shielded: $data['indicatieAfgeschermd'] ?? null,
            fullAddress: $data['volledigAdres'] ?? null,
            streetName: $data['straatnaam'] ?? null,
            houseNumber: $data['huisnummer'] ?? null,
            houseNumberAddition: $data['huisnummerToevoeging'] ?? null,
            houseLetter: $data['huisletter'] ?? null,
            addressAddition: $data['toevoegingAdres'] ?? null,
            postalCode: $data['postcode'] ?? null,
            poBoxNumber: $data['postbusnummer'] ?? null,
            city: $data['plaats'] ?? null,
            streetHouseNumber: $data['straatHuisnummer'] ?? null,
            postalCodeCity: $data['postcodeWoonplaats'] ?? null,
            region: $data['regio'] ?? null,
            country: $data['land'] ?? null,
            geoData: isset($data['geoData'])
                ? GeoData::fromArray($data['geoData'])
                : null,
        );
    }

    public static function fake(
        string $type = 'bezoekadres',
        ?string $shielded = 'Nee',
        ?string $fullAddress = 'Watermolenlaan 1 3447GT Woerden',
        ?string $streetName = 'Watermolenlaan',
        ?int $houseNumber = 1,
        ?string $houseNumberAddition = null,
        ?string $houseLetter = null,
        ?string $addressAddition = null,
        ?string $postalCode = '3447GT',
        ?int $poBoxNumber = null,
        ?string $city = 'Woerden',
        ?string $streetHouseNumber = null,
        ?string $postalCodeCity = null,
        ?string $region = null,
        ?string $country = 'Nederland',
        ?GeoData $geoData = null,
    ): self {
        return new self(
            type: $type,
            shielded: $shielded,
            fullAddress: $fullAddress,
            streetName: $streetName,
            houseNumber: $houseNumber,
            houseNumberAddition: $houseNumberAddition,
            houseLetter: $houseLetter,
            addressAddition: $addressAddition,
            postalCode: $postalCode,
            poBoxNumber: $poBoxNumber,
            city: $city,
            streetHouseNumber: $streetHouseNumber,
            postalCodeCity: $postalCodeCity,
            region: $region,
            country: $country,
            geoData: $geoData,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'indicatieAfgeschermd' => $this->shielded,
            'volledigAdres' => $this->fullAddress,
            'straatnaam' => $this->streetName,
            'huisnummer' => $this->houseNumber,
            'huisnummerToevoeging' => $this->houseNumberAddition,
            'huisletter' => $this->houseLetter,
            'toevoegingAdres' => $this->addressAddition,
            'postcode' => $this->postalCode,
            'postbusnummer' => $this->poBoxNumber,
            'plaats' => $this->city,
            'straatHuisnummer' => $this->streetHouseNumber,
            'postcodeWoonplaats' => $this->postalCodeCity,
            'regio' => $this->region,
            'land' => $this->country,
            'geoData' => $this->geoData?->toArray(),
        ];
    }
}
