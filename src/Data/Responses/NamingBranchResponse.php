<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Data\ValueObjects\TradeName;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class NamingBranchResponse implements Arrayable
{
    /**
     * @param  list<TradeName>  $tradeNames
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $branchNumber,
        public ?string $firstTradeName = null,
        public ?string $name = null,
        public ?string $alsoKnownAs = null,
        public array $tradeNames = [],
        public array $links = [],
    ) {}

    /**
     * @param  array{vestigingsnummer: string, eersteHandelsnaam?: string, handelsnamen?: list<array{naam: string, volgorde: int}>, naam?: string, ookGenoemd?: string, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            branchNumber: $data['vestigingsnummer'],
            firstTradeName: $data['eersteHandelsnaam'] ?? null,
            name: $data['naam'] ?? null,
            alsoKnownAs: $data['ookGenoemd'] ?? null,
            tradeNames: array_map(
                TradeName::fromArray(...),
                $data['handelsnamen'] ?? [],
            ),
            links: array_map(
                Link::fromArray(...),
                $data['links'] ?? [],
            ),
        );
    }

    /**
     * @param  list<TradeName>  $tradeNames
     * @param  list<Link>  $links
     */
    public static function fake(
        string $branchNumber = '000037178598',
        ?string $firstTradeName = 'Test Stichting Bolderbast',
        ?string $name = null,
        ?string $alsoKnownAs = null,
        array $tradeNames = [],
        array $links = [],
    ): self {
        return new self(
            branchNumber: $branchNumber,
            firstTradeName: $firstTradeName,
            name: $name,
            alsoKnownAs: $alsoKnownAs,
            tradeNames: $tradeNames === [] ? [TradeName::fake()] : $tradeNames,
            links: $links,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'vestigingsnummer' => $this->branchNumber,
            'eersteHandelsnaam' => $this->firstTradeName,
            'naam' => $this->name,
            'ookGenoemd' => $this->alsoKnownAs,
            'handelsnamen' => array_map(fn (TradeName $tradeName): array => $tradeName->toArray(), $this->tradeNames),
            'links' => array_map(fn (Link $link): array => $link->toArray(), $this->links),
        ];
    }
}
