<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\DomesticAddress;
use DIJ\Kvk\Data\ValueObjects\ForeignAddress;
use DIJ\Kvk\Data\ValueObjects\SearchResultAddress;
use PHPUnit\Framework\TestCase;

final class SearchResultAddressTest extends TestCase
{
    public function test_from_array_with_domestic_only(): void
    {
        $address = SearchResultAddress::fromArray([
            'binnenlandsAdres' => [
                'type' => 'bezoekadres',
                'straatnaam' => 'Hizzaarderlaan',
                'huisnummer' => 1,
                'postcode' => '1234AB',
                'plaats' => 'Lollum',
            ],
        ]);

        self::assertInstanceOf(DomesticAddress::class, $address->domesticAddress);
        self::assertSame('Hizzaarderlaan', $address->domesticAddress->streetName);
        self::assertNull($address->foreignAddress);
    }

    public function test_from_array_with_foreign_only(): void
    {
        $address = SearchResultAddress::fromArray([
            'buitenlandsAdres' => [
                'straatHuisnummer' => '123 Main Street',
                'postcodeWoonplaats' => '12345 Berlin',
                'land' => 'Duitsland',
            ],
        ]);

        self::assertNull($address->domesticAddress);
        self::assertInstanceOf(ForeignAddress::class, $address->foreignAddress);
        self::assertSame('Duitsland', $address->foreignAddress->country);
    }

    public function test_from_array_with_both(): void
    {
        $address = SearchResultAddress::fromArray([
            'binnenlandsAdres' => [
                'type' => 'bezoekadres',
                'plaats' => 'Lollum',
            ],
            'buitenlandsAdres' => [
                'land' => 'Duitsland',
            ],
        ]);

        self::assertInstanceOf(DomesticAddress::class, $address->domesticAddress);
        self::assertInstanceOf(ForeignAddress::class, $address->foreignAddress);
    }

    public function test_from_array_with_neither(): void
    {
        $address = SearchResultAddress::fromArray([]);

        self::assertNull($address->domesticAddress);
        self::assertNull($address->foreignAddress);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $address = SearchResultAddress::fromArray([
            'binnenlandsAdres' => [
                'type' => 'bezoekadres',
                'straatnaam' => 'Hizzaarderlaan',
            ],
            'buitenlandsAdres' => [
                'land' => 'Duitsland',
            ],
        ]);

        $array = $address->toArray();

        self::assertIsArray($array['binnenlandsAdres']);
        self::assertSame('bezoekadres', $array['binnenlandsAdres']['type']);
        self::assertIsArray($array['buitenlandsAdres']);
        self::assertSame('Duitsland', $array['buitenlandsAdres']['land']);
    }

    public function test_to_array_returns_null_for_absent_addresses(): void
    {
        $address = SearchResultAddress::fromArray([]);

        $array = $address->toArray();

        self::assertNull($array['binnenlandsAdres']);
        self::assertNull($array['buitenlandsAdres']);
    }
}
