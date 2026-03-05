<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Results;

use DIJ\Kvk\Collections\SignalListItemCollection;
use DIJ\Kvk\Data\Responses\SignalListItem;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SignalsResult implements Arrayable
{
    public function __construct(
        public SignalListItemCollection $signals,
        public int $page,
        public int $resultsPerPage,
        public int $total,
        public int $totalPages,
        public ?string $previous = null,
        public ?string $next = null,
    ) {}

    /**
     * @param  array{pagina: int, aantal: int, totaal: int, totaalPaginas: int, signalen: list<array{id: string, timestamp: string, kvknummer: string, signaalType: string, vestigingsnummer?: string}>, vorige?: string, volgende?: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            signals: new SignalListItemCollection(
                array_map(
                    SignalListItem::fromArray(...),
                    $data['signalen'],
                ),
            ),
            page: $data['pagina'],
            resultsPerPage: $data['aantal'],
            total: $data['totaal'],
            totalPages: $data['totaalPaginas'],
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

        /** @var array{pagina: int, aantal: int, totaal: int, totaalPaginas: int, signalen: list<array{id: string, timestamp: string, kvknummer: string, signaalType: string, vestigingsnummer?: string}>, vorige?: string, volgende?: string} $body */
        return self::fromArray($body);
    }

    public static function fake(?SignalListItemCollection $signals = null, int $page = 1, int $resultsPerPage = 100, int $total = 1, int $totalPages = 1, ?string $previous = null, ?string $next = null): self
    {
        $signals ??= new SignalListItemCollection([SignalListItem::fake()]);

        return new self(
            signals: $signals,
            page: $page,
            resultsPerPage: $resultsPerPage,
            total: $total,
            totalPages: $totalPages,
            previous: $previous,
            next: $next,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'signalen' => $this->signals->toArray(),
            'pagina' => $this->page,
            'aantal' => $this->resultsPerPage,
            'totaal' => $this->total,
            'totaalPaginas' => $this->totalPages,
            'vorige' => $this->previous,
            'volgende' => $this->next,
        ];
    }
}
