<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\ValueObjects\Address;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class BaseProfileOwnerResponseTest extends TestCase
{
    public function test_from_array_with_all_fields(): void
    {
        $data = [
            'rsin' => '123456789',
            'rechtsvorm' => 'BesloteVennootschap',
            'uitgebreideRechtsvorm' => 'Besloten Vennootschap met gewone structuur',
            'adressen' => [
                [
                    'type' => 'bezoekadres',
                    'straatnaam' => 'Watermolenlaan',
                    'huisnummer' => 1,
                    'postcode' => '3447GT',
                    'plaats' => 'Woerden',
                ],
            ],
            'websites' => ['https://example.com'],
            'links' => [
                [
                    'rel' => 'basisprofiel',
                    'href' => 'https://api.kvk.nl/api/v1/basisprofielen/69599068',
                ],
            ],
        ];

        $owner = BaseProfileOwnerResponse::fromArray($data);

        self::assertSame('123456789', $owner->rsin);
        self::assertSame('BesloteVennootschap', $owner->legalForm);
        self::assertSame('Besloten Vennootschap met gewone structuur', $owner->extendedLegalForm);
        self::assertCount(1, $owner->addresses);
        self::assertInstanceOf(Address::class, $owner->addresses[0]);
        self::assertSame('bezoekadres', $owner->addresses[0]->type);
        self::assertCount(1, $owner->websites);
        self::assertSame('https://example.com', $owner->websites[0]);
        self::assertCount(1, $owner->links);
        self::assertInstanceOf(Link::class, $owner->links[0]);
        self::assertSame('basisprofiel', $owner->links[0]->rel);
    }

    public function test_from_array_with_empty_collections(): void
    {
        $data = [
            'rsin' => '123456789',
            'rechtsvorm' => 'Stichting',
        ];

        $owner = BaseProfileOwnerResponse::fromArray($data);

        self::assertSame('123456789', $owner->rsin);
        self::assertSame('Stichting', $owner->legalForm);
        self::assertNull($owner->extendedLegalForm);
        self::assertCount(0, $owner->addresses);
        self::assertCount(0, $owner->websites);
        self::assertCount(0, $owner->links);
    }

    public function test_from_array_websites_are_strings(): void
    {
        $data = [
            'websites' => ['https://example.com', 'https://another.com'],
        ];

        $owner = BaseProfileOwnerResponse::fromArray($data);

        self::assertCount(2, $owner->websites);
        self::assertIsString($owner->websites[0]);
        self::assertIsString($owner->websites[1]);
        self::assertSame('https://example.com', $owner->websites[0]);
        self::assertSame('https://another.com', $owner->websites[1]);
    }

    public function test_from_response_parses_valid_json(): void
    {
        $json = json_encode([
            'rsin' => '123456789',
            'rechtsvorm' => 'BesloteVennootschap',
            'uitgebreideRechtsvorm' => 'Besloten Vennootschap met gewone structuur',
            'adressen' => [
                [
                    'type' => 'bezoekadres',
                    'straatnaam' => 'Watermolenlaan',
                    'huisnummer' => 1,
                    'postcode' => '3447GT',
                    'plaats' => 'Woerden',
                ],
            ],
            'websites' => ['https://example.com'],
            'links' => [],
        ], JSON_THROW_ON_ERROR);

        $response = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], $json),
        );

        $owner = BaseProfileOwnerResponse::fromResponse($response);

        self::assertInstanceOf(BaseProfileOwnerResponse::class, $owner);
        self::assertSame('123456789', $owner->rsin);
        self::assertSame('BesloteVennootschap', $owner->legalForm);
        self::assertCount(1, $owner->addresses);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $response = new Response(
            new Psr7Response(200, [], '<html>Server Error</html>'),
        );

        try {
            BaseProfileOwnerResponse::fromResponse($response);
            self::fail('Expected KvkException');
        } catch (KvkException $e) {
            self::assertSame(200, $e->statusCode);
            self::assertSame('<html>Server Error</html>', $e->responseBody);
            self::assertSame('KVK API returned an invalid response body', $e->getMessage());
        }
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $owner = BaseProfileOwnerResponse::fake();

        self::assertSame('123456789', $owner->rsin);
        self::assertSame('BesloteVennootschap', $owner->legalForm);
        self::assertSame('Besloten Vennootschap met gewone structuur', $owner->extendedLegalForm);
        self::assertCount(1, $owner->addresses);
        self::assertInstanceOf(Address::class, $owner->addresses[0]);
        self::assertCount(1, $owner->websites);
        self::assertSame('https://example.com', $owner->websites[0]);
    }
}
