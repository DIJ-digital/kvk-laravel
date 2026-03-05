<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Data\ValueObjects\SearchResultAddress;
use PHPUnit\Framework\TestCase;

final class SearchResponseTest extends TestCase
{
    public function test_from_array_with_all_fields(): void
    {
        $response = SearchResponse::fromArray([
            'kvkNummer' => '69599068',
            'rsin' => '123456789',
            'vestigingsnummer' => '000037178598',
            'naam' => 'Test BV Donald',
            'adres' => [
                'binnenlandsAdres' => [
                    'type' => 'bezoekadres',
                    'straatnaam' => 'Hizzaarderlaan',
                    'huisnummer' => 1,
                    'huisletter' => 'A',
                    'postbusnummer' => 123,
                    'postcode' => '1234AB',
                    'plaats' => 'Lollum',
                ],
            ],
            'type' => 'hoofdvestiging',
            'actief' => 'Ja',
            'vervallenNaam' => 'Oude Naam BV',
            'links' => [
                [
                    'rel' => 'basisprofiel',
                    'href' => 'https://api.kvk.nl/api/v1/basisprofielen/69599068',
                ],
                [
                    'rel' => 'vestigingsprofiel',
                    'href' => 'https://api.kvk.nl/api/v1/vestigingsprofielen/000037178598',
                ],
            ],
        ]);

        self::assertSame('69599068', $response->kvkNumber);
        self::assertSame('123456789', $response->rsin);
        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('Test BV Donald', $response->name);
        self::assertInstanceOf(SearchResultAddress::class, $response->address);
        self::assertSame('Hizzaarderlaan', $response->address->domesticAddress?->streetName);
        self::assertSame('hoofdvestiging', $response->type);
        self::assertSame('Ja', $response->active);
        self::assertSame('Oude Naam BV', $response->expiredName);
        self::assertCount(2, $response->links);
        self::assertInstanceOf(Link::class, $response->links[0]);
        self::assertSame('basisprofiel', $response->links[0]->rel);
    }

    public function test_from_array_with_minimal_fields(): void
    {
        $response = SearchResponse::fromArray([
            'kvkNummer' => '69599068',
            'naam' => 'Test Stichting',
            'type' => 'rechtspersoon',
            'actief' => 'Nee',
        ]);

        self::assertSame('69599068', $response->kvkNumber);
        self::assertSame('Test Stichting', $response->name);
        self::assertSame('rechtspersoon', $response->type);
        self::assertSame('Nee', $response->active);
        self::assertNull($response->rsin);
        self::assertNull($response->branchNumber);
        self::assertNull($response->address);
        self::assertNull($response->expiredName);
        self::assertSame([], $response->links);
    }

    public function test_from_array_with_foreign_address(): void
    {
        $response = SearchResponse::fromArray([
            'kvkNummer' => '69599068',
            'naam' => 'Test Foreign BV',
            'type' => 'hoofdvestiging',
            'actief' => 'Ja',
            'adres' => [
                'buitenlandsAdres' => [
                    'straatHuisnummer' => '123 Main Street',
                    'postcodeWoonplaats' => '12345 Berlin',
                    'land' => 'Duitsland',
                ],
            ],
        ]);

        self::assertNotNull($response->address);
        self::assertNull($response->address->domesticAddress);
        self::assertNotNull($response->address->foreignAddress);
        self::assertSame('Duitsland', $response->address->foreignAddress->country);
    }

    public function test_from_array_with_empty_links(): void
    {
        $response = SearchResponse::fromArray([
            'kvkNummer' => '69599068',
            'naam' => 'Test',
            'type' => 'hoofdvestiging',
            'actief' => 'Ja',
            'links' => [],
        ]);

        self::assertSame([], $response->links);
    }

    public function test_fake_returns_instance_with_defaults(): void
    {
        $response = SearchResponse::fake();

        self::assertSame('69599068', $response->kvkNumber);
        self::assertSame('Test BV Donald', $response->name);
        self::assertSame('hoofdvestiging', $response->type);
        self::assertSame('Ja', $response->active);
        self::assertNull($response->rsin);
        self::assertSame('000037178598', $response->branchNumber);
        self::assertNull($response->address);
        self::assertNull($response->expiredName);
        self::assertSame([], $response->links);
    }

    public function test_fake_accepts_overrides(): void
    {
        $response = SearchResponse::fake(kvkNumber: '99999999', name: 'Custom BV');

        self::assertSame('99999999', $response->kvkNumber);
        self::assertSame('Custom BV', $response->name);
        self::assertSame('hoofdvestiging', $response->type);
        self::assertSame('Ja', $response->active);
        self::assertSame('000037178598', $response->branchNumber);
    }

    public function test_fake_returns_new_instance_each_call(): void
    {
        $a = SearchResponse::fake();
        $b = SearchResponse::fake();

        self::assertNotSame($a, $b);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $response = SearchResponse::fake();

        $array = $response->toArray();

        self::assertSame('69599068', $array['kvkNummer']);
        self::assertSame('Test BV Donald', $array['naam']);
        self::assertSame('hoofdvestiging', $array['type']);
        self::assertSame('Ja', $array['actief']);
        self::assertNull($array['rsin']);
        self::assertSame('000037178598', $array['vestigingsnummer']);
        self::assertNull($array['adres']);
        self::assertNull($array['vervallenNaam']);
        self::assertSame([], $array['links']);
    }
}
