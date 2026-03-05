<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Collections\NamingBranchCollection;
use DIJ\Kvk\Data\Responses\NamingBranchResponse;
use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class NamingResponseTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $response = NamingResponse::fromArray([
            'kvkNummer' => '69599068',
            'statutaireNaam' => 'Stichting Bolderbast',
            'naam' => 'Test Stichting Bolderbast',
            'vestigingen' => [
                [
                    'vestigingsnummer' => '000037178598',
                    'eersteHandelsnaam' => 'Test Stichting Bolderbast',
                ],
            ],
        ]);

        self::assertSame('69599068', $response->kvkNumber);
        self::assertSame('Stichting Bolderbast', $response->statutoryName);
        self::assertSame('Test Stichting Bolderbast', $response->name);
        self::assertNull($response->rsin);
        self::assertNull($response->alsoKnownAs);
        self::assertNull($response->startDate);
        self::assertNull($response->endDate);
        self::assertInstanceOf(NamingBranchCollection::class, $response->branches);
        self::assertCount(1, $response->branches);
        self::assertSame([], $response->links);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $response = NamingResponse::fromArray([
            'kvkNummer' => '69599068',
            'rsin' => '123456789',
            'statutaireNaam' => 'Stichting Bolderbast',
            'naam' => 'Test Stichting Bolderbast',
            'ookGenoemd' => 'Bolderbast',
            'startdatum' => '20150101',
            'einddatum' => null,
            'vestigingen' => [
                [
                    'vestigingsnummer' => '000037178598',
                    'eersteHandelsnaam' => 'Test Stichting Bolderbast',
                    'handelsnamen' => [
                        ['naam' => 'Test Stichting Bolderbast', 'volgorde' => 1],
                    ],
                ],
                [
                    'vestigingsnummer' => '000037178599',
                    'naam' => 'Stichting Branch',
                    'ookGenoemd' => 'Branch Alias',
                ],
            ],
            'links' => [
                ['rel' => 'self', 'href' => 'https://api.kvk.nl/api/v1/naamgevingen/kvknummer/69599068'],
            ],
        ]);

        self::assertSame('123456789', $response->rsin);
        self::assertSame('Bolderbast', $response->alsoKnownAs);
        self::assertSame('20150101', $response->startDate);
        self::assertNull($response->endDate);
        self::assertCount(2, $response->branches);
        self::assertInstanceOf(NamingBranchResponse::class, $response->branches[0]);
        self::assertInstanceOf(NamingBranchResponse::class, $response->branches[1]);
        self::assertCount(1, $response->links);
    }

    public function test_from_response_parses_valid_json(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'statutaireNaam' => 'Stichting Bolderbast',
                'naam' => 'Test Stichting Bolderbast',
                'vestigingen' => [
                    [
                        'vestigingsnummer' => '000037178598',
                        'eersteHandelsnaam' => 'Test Stichting Bolderbast',
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $result = NamingResponse::fromResponse($httpResponse);

        self::assertSame('69599068', $result->kvkNumber);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode('not-an-array', JSON_THROW_ON_ERROR)),
        );

        $this->expectException(KvkException::class);

        NamingResponse::fromResponse($httpResponse);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $response = NamingResponse::fake();

        self::assertSame('69599068', $response->kvkNumber);
        self::assertSame('Stichting Bolderbast', $response->statutoryName);
        self::assertSame('Test Stichting Bolderbast', $response->name);
        self::assertSame('123456789', $response->rsin);
        self::assertSame('Bolderbast', $response->alsoKnownAs);
        self::assertSame('20150101', $response->startDate);
        self::assertNull($response->endDate);
        self::assertCount(1, $response->branches);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $response = NamingResponse::fake();

        $array = $response->toArray();

        self::assertSame('69599068', $array['kvkNummer']);
        self::assertSame('Stichting Bolderbast', $array['statutaireNaam']);
        self::assertSame('Test Stichting Bolderbast', $array['naam']);
        self::assertIsArray($array['vestigingen']);
        self::assertCount(1, $array['vestigingen']);
        self::assertSame('123456789', $array['rsin']);
        self::assertSame('Bolderbast', $array['ookGenoemd']);
        self::assertSame('20150101', $array['startdatum']);
        self::assertNull($array['einddatum']);
        self::assertSame([], $array['links']);
    }
}
