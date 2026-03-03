<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Collections\SearchResponseCollection;
use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Data\Results\SearchResult;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SearchResultTest extends TestCase
{
    public function test_from_array_parses_items_and_metadata(): void
    {
        $result = SearchResult::fromArray([
            'pagina' => 1,
            'resultatenPerPagina' => 10,
            'totaal' => 2,
            'resultaten' => [
                [
                    'kvkNummer' => '69599068',
                    'naam' => 'Test BV Donald',
                    'type' => 'hoofdvestiging',
                    'actief' => 'Ja',
                    'vestigingsnummer' => '000037178598',
                ],
                [
                    'kvkNummer' => '68750110',
                    'naam' => 'Test BV Dagobert',
                    'type' => 'nevenvestiging',
                    'actief' => 'Ja',
                ],
            ],
        ]);

        self::assertInstanceOf(SearchResponseCollection::class, $result->items);
        self::assertCount(2, $result->items);
        self::assertInstanceOf(SearchResponse::class, $result->items->first());
        self::assertSame('69599068', $result->items->first()->kvkNumber);
        self::assertSame('68750110', $result->items->last()->kvkNumber);
        self::assertSame(1, $result->page);
        self::assertSame(10, $result->resultsPerPage);
        self::assertSame(2, $result->total);
        self::assertNull($result->previous);
        self::assertNull($result->next);
    }

    public function test_from_array_with_pagination_links(): void
    {
        $result = SearchResult::fromArray([
            'pagina' => 3,
            'resultatenPerPagina' => 10,
            'totaal' => 42,
            'vorige' => 'https://api.kvk.nl/api/v2/zoeken?pagina=2',
            'volgende' => 'https://api.kvk.nl/api/v2/zoeken?pagina=4',
            'resultaten' => [
                [
                    'kvkNummer' => '69599068',
                    'naam' => 'Test',
                    'type' => 'hoofdvestiging',
                    'actief' => 'Ja',
                ],
            ],
        ]);

        self::assertSame(3, $result->page);
        self::assertSame(42, $result->total);
        self::assertSame('https://api.kvk.nl/api/v2/zoeken?pagina=2', $result->previous);
        self::assertSame('https://api.kvk.nl/api/v2/zoeken?pagina=4', $result->next);
    }

    public function test_from_array_with_empty_results(): void
    {
        $result = SearchResult::fromArray([
            'pagina' => 1,
            'resultatenPerPagina' => 10,
            'totaal' => 0,
            'resultaten' => [],
        ]);

        self::assertCount(0, $result->items);
        self::assertSame(0, $result->total);
    }

    public function test_from_response_delegates_to_from_array(): void
    {
        $json = json_encode([
            'pagina' => 1,
            'resultatenPerPagina' => 10,
            'totaal' => 1,
            'resultaten' => [
                [
                    'kvkNummer' => '69599068',
                    'naam' => 'Test BV',
                    'type' => 'hoofdvestiging',
                    'actief' => 'Ja',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $response = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], $json),
        );

        $result = SearchResult::fromResponse($response);

        self::assertSame(1, $result->page);
        self::assertCount(1, $result->items);
        self::assertSame('69599068', $result->items->first()->kvkNumber);
    }

    public function test_from_response_throws_on_non_json_body(): void
    {
        $response = new Response(
            new Psr7Response(200, [], '<html>Server Error</html>'),
        );

        try {
            SearchResult::fromResponse($response);
            self::fail('Expected KvkException');
        } catch (KvkException $e) {
            self::assertSame(200, $e->statusCode);
            self::assertSame('<html>Server Error</html>', $e->responseBody);
            self::assertSame('KVK API returned an invalid response body', $e->getMessage());
        }
    }

    public function test_from_response_throws_on_empty_body(): void
    {
        $response = new Response(
            new Psr7Response(200, [], ''),
        );

        try {
            SearchResult::fromResponse($response);
            self::fail('Expected KvkException');
        } catch (KvkException $e) {
            self::assertSame(200, $e->statusCode);
            self::assertSame('', $e->responseBody);
        }
    }
}
