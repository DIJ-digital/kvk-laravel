<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Collections\SignalListItemCollection;
use DIJ\Kvk\Data\Responses\SignalListItem;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SignalsResultTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $result = SignalsResult::fromArray([
            'pagina' => 1,
            'aantal' => 100,
            'totaal' => 250,
            'totaalPaginas' => 3,
            'signalen' => [
                [
                    'id' => 'signal-001',
                    'timestamp' => '2024-05-14T15:25:13.773Z',
                    'kvknummer' => '69792917',
                    'signaalType' => 'SignaalGewijzigdeInschrijving',
                ],
            ],
        ]);

        self::assertSame(1, $result->page);
        self::assertSame(100, $result->resultsPerPage);
        self::assertSame(250, $result->total);
        self::assertSame(3, $result->totalPages);
        self::assertInstanceOf(SignalListItemCollection::class, $result->signals);
        self::assertCount(1, $result->signals);
        self::assertInstanceOf(SignalListItem::class, $result->signals[0]);
        self::assertNull($result->previous);
        self::assertNull($result->next);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $result = SignalsResult::fromArray([
            'pagina' => 2,
            'aantal' => 50,
            'totaal' => 100,
            'totaalPaginas' => 2,
            'signalen' => [],
            'vorige' => 'https://api.kvk.nl/api/v1/abonnementen/sub-1?pagina=1',
            'volgende' => 'https://api.kvk.nl/api/v1/abonnementen/sub-1?pagina=3',
        ]);

        self::assertSame('https://api.kvk.nl/api/v1/abonnementen/sub-1?pagina=1', $result->previous);
        self::assertSame('https://api.kvk.nl/api/v1/abonnementen/sub-1?pagina=3', $result->next);
    }

    public function test_from_response_parses_valid_json(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'aantal' => 100,
                'totaal' => 1,
                'totaalPaginas' => 1,
                'signalen' => [
                    [
                        'id' => 'signal-001',
                        'timestamp' => '2024-05-14T15:25:13.773Z',
                        'kvknummer' => '69792917',
                        'signaalType' => 'SignaalGewijzigdeInschrijving',
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $result = SignalsResult::fromResponse($httpResponse);

        self::assertSame(1, $result->page);
        self::assertCount(1, $result->signals);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode('not-an-array', JSON_THROW_ON_ERROR)),
        );

        $this->expectException(KvkException::class);

        SignalsResult::fromResponse($httpResponse);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $result = SignalsResult::fake();

        self::assertSame(1, $result->page);
        self::assertSame(100, $result->resultsPerPage);
        self::assertSame(1, $result->total);
        self::assertSame(1, $result->totalPages);
        self::assertCount(1, $result->signals);
        self::assertNull($result->previous);
        self::assertNull($result->next);
    }
}
