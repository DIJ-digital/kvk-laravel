<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Results;

use DIJ\Kvk\Collections\SearchResponseCollection;
use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SearchResult implements Arrayable
{
    public function __construct(
        public SearchResponseCollection $items,
        public int $page,
        public int $resultsPerPage,
        public int $total,
        public ?string $previous = null,
        public ?string $next = null,
    ) {}

    /**
     * @param  array{pagina: int, resultatenPerPagina: int, totaal: int, vorige?: string, volgende?: string, resultaten: list<array{kvkNummer: string, naam: string, type: string, actief: string, rsin?: string, vestigingsnummer?: string, adres?: array{binnenlandsAdres?: array{type: string, straatnaam?: string, huisnummer?: int, huisletter?: string, postbusnummer?: int, postcode?: string, plaats?: string}, buitenlandsAdres?: array{straatHuisnummer?: string, postcodeWoonplaats?: string, land?: string}}, vervallenNaam?: string, links?: list<array{rel: string, href: string}>}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            items: new SearchResponseCollection(
                array_map(
                    SearchResponse::fromArray(...),
                    $data['resultaten'],
                ),
            ),
            page: $data['pagina'],
            resultsPerPage: $data['resultatenPerPagina'],
            total: $data['totaal'],
            previous: $data['vorige'] ?? null,
            next: $data['volgende'] ?? null,
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

        /** @var array{pagina: int, resultatenPerPagina: int, totaal: int, vorige?: string, volgende?: string, resultaten: list<array{kvkNummer: string, naam: string, type: string, actief: string, rsin?: string, vestigingsnummer?: string, adres?: array{binnenlandsAdres?: array{type: string, straatnaam?: string, huisnummer?: int, huisletter?: string, postbusnummer?: int, postcode?: string, plaats?: string}, buitenlandsAdres?: array{straatHuisnummer?: string, postcodeWoonplaats?: string, land?: string}}, vervallenNaam?: string, links?: list<array{rel: string, href: string}>}>} $body */
        return self::fromArray($body);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'resultaten' => $this->items->toArray(),
            'pagina' => $this->page,
            'resultatenPerPagina' => $this->resultsPerPage,
            'totaal' => $this->total,
            'vorige' => $this->previous,
            'volgende' => $this->next,
        ];
    }
}
