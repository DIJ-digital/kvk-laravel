<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Collections\BaseProfileBranchCollection;
use DIJ\Kvk\Data\Responses\BaseProfileBranchResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class BaseProfileBranchesResultTest extends TestCase
{
    public function test_from_array_maps_all_fields(): void
    {
        $data = [
            'kvkNummer' => '69599068',
            'aantalCommercieleVestigingen' => 1,
            'aantalNietCommercieleVestigingen' => 0,
            'totaalAantalVestigingen' => 1,
            'vestigingen' => [
                [
                    'vestigingsnummer' => '000037178598',
                    'eersteHandelsnaam' => 'Test BV Donald',
                    'indHoofdvestiging' => 'Ja',
                    'indCommercieleVestiging' => 'Ja',
                    'volledigAdres' => 'Hizzaarderlaan 1 1234AB Lollum',
                ],
            ],
        ];

        $result = BaseProfileBranchesResult::fromArray($data);

        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame(1, $result->commercialBranchCount);
        self::assertSame(0, $result->nonCommercialBranchCount);
        self::assertSame(1, $result->totalBranchCount);
        self::assertInstanceOf(BaseProfileBranchCollection::class, $result->branches);
        self::assertCount(1, $result->branches);
        self::assertInstanceOf(BaseProfileBranchResponse::class, $result->branches->first());
    }

    public function test_from_array_with_empty_branches(): void
    {
        $data = [
            'kvkNummer' => '69599068',
            'aantalCommercieleVestigingen' => 0,
            'aantalNietCommercieleVestigingen' => 0,
            'totaalAantalVestigingen' => 0,
            'vestigingen' => [],
        ];

        $result = BaseProfileBranchesResult::fromArray($data);

        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame(0, $result->commercialBranchCount);
        self::assertSame(0, $result->nonCommercialBranchCount);
        self::assertSame(0, $result->totalBranchCount);
        self::assertTrue($result->branches->isEmpty());
    }

    public function test_from_response_parses_valid_json(): void
    {
        $psr7Response = new Psr7Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'kvkNummer' => '69599068',
                'aantalCommercieleVestigingen' => 1,
                'aantalNietCommercieleVestigingen' => 0,
                'totaalAantalVestigingen' => 1,
                'vestigingen' => [
                    [
                        'vestigingsnummer' => '000037178598',
                        'eersteHandelsnaam' => 'Test BV Donald',
                        'indHoofdvestiging' => 'Ja',
                        'indCommercieleVestiging' => 'Ja',
                        'volledigAdres' => 'Hizzaarderlaan 1 1234AB Lollum',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $response = new Response($psr7Response);
        $result = BaseProfileBranchesResult::fromResponse($response);

        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame(1, $result->totalBranchCount);
        self::assertCount(1, $result->branches);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $psr7Response = new Psr7Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode('string', JSON_THROW_ON_ERROR),
        );

        $response = new Response($psr7Response);

        self::expectException(KvkException::class);
        self::expectExceptionMessage('KVK API returned an invalid response body');

        BaseProfileBranchesResult::fromResponse($response);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $result = BaseProfileBranchesResult::fake();

        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame(1, $result->commercialBranchCount);
        self::assertSame(0, $result->nonCommercialBranchCount);
        self::assertSame(1, $result->totalBranchCount);
        self::assertCount(1, $result->branches);
        self::assertInstanceOf(BaseProfileBranchResponse::class, $result->branches->first());
    }
}
