<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Link;

readonly class BaseProfileBranchResponse
{
    /**
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $branchNumber,
        public string $firstTradeName,
        public string $mainBranchIndicator,
        public string $commercialBranchIndicator,
        public ?string $fullAddress = null,
        public array $links = [],
    ) {}

    /**
     * @param  array{vestigingsnummer: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, volledigAdres?: string, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            branchNumber: $data['vestigingsnummer'],
            firstTradeName: $data['eersteHandelsnaam'],
            mainBranchIndicator: $data['indHoofdvestiging'],
            commercialBranchIndicator: $data['indCommercieleVestiging'],
            fullAddress: $data['volledigAdres'] ?? null,
            links: array_map(
                Link::fromArray(...),
                $data['links'] ?? [],
            ),
        );
    }

    /**
     * @param  list<Link>  $links
     */
    public static function fake(
        string $branchNumber = '000037178598',
        string $firstTradeName = 'Test BV Donald',
        string $mainBranchIndicator = 'Ja',
        string $commercialBranchIndicator = 'Ja',
        ?string $fullAddress = 'Hizzaarderlaan 1 1234AB Lollum',
        array $links = [],
    ): self {
        return new self(
            branchNumber: $branchNumber,
            firstTradeName: $firstTradeName,
            mainBranchIndicator: $mainBranchIndicator,
            commercialBranchIndicator: $commercialBranchIndicator,
            fullAddress: $fullAddress,
            links: $links,
        );
    }
}
