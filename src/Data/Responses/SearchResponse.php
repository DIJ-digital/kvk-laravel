<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Data\ValueObjects\SearchResultAddress;

readonly class SearchResponse
{
    /**
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $kvkNumber,
        public string $name,
        public string $type,
        public ?string $active = null,
        public ?string $rsin = null,
        public ?string $branchNumber = null,
        public ?SearchResultAddress $address = null,
        public ?string $expiredName = null,
        public array $links = [],
    ) {}

    /**
     * @param  array{kvkNummer: string, naam: string, type: string, actief?: string, rsin?: string, vestigingsnummer?: string, adres?: array{binnenlandsAdres?: array{type: string, straatnaam?: string, huisnummer?: int, huisletter?: string, postbusnummer?: int, postcode?: string, plaats?: string}, buitenlandsAdres?: array{straatHuisnummer?: string, postcodeWoonplaats?: string, land?: string}}, vervallenNaam?: string, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            kvkNumber: $data['kvkNummer'],
            name: $data['naam'],
            type: $data['type'],
            active: $data['actief'] ?? null,
            rsin: $data['rsin'] ?? null,
            branchNumber: $data['vestigingsnummer'] ?? null,
            address: isset($data['adres'])
                ? SearchResultAddress::fromArray($data['adres'])
                : null,
            expiredName: $data['vervallenNaam'] ?? null,
            links: array_map(
                Link::fromArray(...),
                $data['links'] ?? [],
            ),
        );
    }

    /**
     * @param  list<Link>  $links
     */
    public static function fake(string $kvkNumber = '69599068', string $name = 'Test BV Donald', string $type = 'hoofdvestiging', string $active = 'Ja', ?string $rsin = null, ?string $branchNumber = '000037178598', ?SearchResultAddress $address = null, ?string $expiredName = null, array $links = []): self
    {
        return new self(
            kvkNumber: $kvkNumber,
            name: $name,
            type: $type,
            active: $active,
            rsin: $rsin,
            branchNumber: $branchNumber,
            address: $address,
            expiredName: $expiredName,
            links: $links,
        );
    }
}
