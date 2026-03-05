<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Results;

use DIJ\Kvk\Collections\BaseProfileBranchCollection;
use DIJ\Kvk\Data\Responses\BaseProfileBranchResponse;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class BaseProfileBranchesResult implements Arrayable
{
    /**
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $kvkNumber,
        public int $commercialBranchCount,
        public int $nonCommercialBranchCount,
        public int $totalBranchCount,
        public BaseProfileBranchCollection $branches,
        public array $links = [],
    ) {}

    /**
     * @param  array{kvkNummer: string, aantalCommercieleVestigingen: int, aantalNietCommercieleVestigingen: int, totaalAantalVestigingen: int, vestigingen: list<array{vestigingsnummer: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, volledigAdres?: string, links?: list<array{rel: string, href: string}>}>, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            kvkNumber: $data['kvkNummer'],
            commercialBranchCount: $data['aantalCommercieleVestigingen'],
            nonCommercialBranchCount: $data['aantalNietCommercieleVestigingen'],
            totalBranchCount: $data['totaalAantalVestigingen'],
            branches: new BaseProfileBranchCollection(
                array_map(
                    BaseProfileBranchResponse::fromArray(...),
                    $data['vestigingen'],
                ),
            ),
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

        /** @var array{kvkNummer: string, aantalCommercieleVestigingen: int, aantalNietCommercieleVestigingen: int, totaalAantalVestigingen: int, vestigingen: list<array{vestigingsnummer: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, volledigAdres?: string, links?: list<array{rel: string, href: string}>}>, links?: list<array{rel: string, href: string}>} $body */
        return self::fromArray($body);
    }

    /**
     * @param  list<Link>  $links
     */
    public static function fake(string $kvkNumber = '69599068', int $commercialBranchCount = 1, int $nonCommercialBranchCount = 0, int $totalBranchCount = 1, ?BaseProfileBranchCollection $branches = null, array $links = []): self
    {
        $branches ??= new BaseProfileBranchCollection([BaseProfileBranchResponse::fake()]);

        return new self(
            kvkNumber: $kvkNumber,
            commercialBranchCount: $commercialBranchCount,
            nonCommercialBranchCount: $nonCommercialBranchCount,
            totalBranchCount: $totalBranchCount,
            branches: $branches,
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
            'aantalCommercieleVestigingen' => $this->commercialBranchCount,
            'aantalNietCommercieleVestigingen' => $this->nonCommercialBranchCount,
            'totaalAantalVestigingen' => $this->totalBranchCount,
            'vestigingen' => $this->branches->toArray(),
            'links' => array_map(fn (Link $link): array => $link->toArray(), $this->links),
        ];
    }
}
