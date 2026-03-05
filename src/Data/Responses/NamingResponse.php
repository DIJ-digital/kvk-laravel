<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Collections\NamingBranchCollection;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class NamingResponse implements Arrayable
{
    /**
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $kvkNumber,
        public string $statutoryName,
        public string $name,
        public NamingBranchCollection $branches,
        public ?string $rsin = null,
        public ?string $alsoKnownAs = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public array $links = [],
    ) {}

    /**
     * @param  array{kvkNummer: string, statutaireNaam: string, naam: string, vestigingen: list<array{vestigingsnummer: string, eersteHandelsnaam?: string, handelsnamen?: list<array{naam: string, volgorde: int}>, naam?: string, ookGenoemd?: string, links?: list<array{rel: string, href: string}>}>, rsin?: string, ookGenoemd?: string, startdatum?: string, einddatum?: string|null, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            kvkNumber: $data['kvkNummer'],
            statutoryName: $data['statutaireNaam'],
            name: $data['naam'],
            branches: new NamingBranchCollection(
                array_map(
                    NamingBranchResponse::fromArray(...),
                    $data['vestigingen'],
                ),
            ),
            rsin: $data['rsin'] ?? null,
            alsoKnownAs: $data['ookGenoemd'] ?? null,
            startDate: $data['startdatum'] ?? null,
            endDate: $data['einddatum'] ?? null,
            links: array_map(
                Link::fromArray(...),
                $data['links'] ?? [],
            ),
        );
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        if (! is_array($body)) {
            throw new KvkException(
                'KVK API returned an invalid response body',
                $response->status(),
                $response->body(),
            );
        }

        /** @var array{kvkNummer: string, statutaireNaam: string, naam: string, vestigingen: list<array{vestigingsnummer: string, eersteHandelsnaam?: string, handelsnamen?: list<array{naam: string, volgorde: int}>, naam?: string, ookGenoemd?: string, links?: list<array{rel: string, href: string}>}>, rsin?: string, ookGenoemd?: string, startdatum?: string, einddatum?: string|null, links?: list<array{rel: string, href: string}>} $body */
        return self::fromArray($body);
    }

    /**
     * @param  list<Link>  $links
     */
    public static function fake(
        string $kvkNumber = '69599068',
        string $statutoryName = 'Stichting Bolderbast',
        string $name = 'Test Stichting Bolderbast',
        ?NamingBranchCollection $branches = null,
        ?string $rsin = '123456789',
        ?string $alsoKnownAs = 'Bolderbast',
        ?string $startDate = '20150101',
        ?string $endDate = null,
        array $links = [],
    ): self {
        $branches ??= new NamingBranchCollection([NamingBranchResponse::fake()]);

        return new self(
            kvkNumber: $kvkNumber,
            statutoryName: $statutoryName,
            name: $name,
            branches: $branches,
            rsin: $rsin,
            alsoKnownAs: $alsoKnownAs,
            startDate: $startDate,
            endDate: $endDate,
            links: $links,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'kvkNummer' => $this->kvkNumber,
            'statutaireNaam' => $this->statutoryName,
            'naam' => $this->name,
            'vestigingen' => $this->branches->toArray(),
            'rsin' => $this->rsin,
            'ookGenoemd' => $this->alsoKnownAs,
            'startdatum' => $this->startDate,
            'einddatum' => $this->endDate,
            'links' => array_map(fn (Link $link): array => $link->toArray(), $this->links),
        ];
    }
}
