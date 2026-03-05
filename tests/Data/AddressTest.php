<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\Address;
use DIJ\Kvk\Data\ValueObjects\GeoData;
use PHPUnit\Framework\TestCase;

final class AddressTest extends TestCase
{
    public function test_from_array_maps_required_type_field(): void
    {
        $data = ['type' => 'bezoekadres'];

        $address = Address::fromArray($data);

        self::assertSame('bezoekadres', $address->type);
    }

    public function test_from_array_maps_all_optional_fields(): void
    {
        $data = [
            'type' => 'correspondentieadres',
            'indicatieAfgeschermd' => 'Ja',
            'volledigAdres' => 'Watermolenlaan 1 3447GT Woerden',
            'straatnaam' => 'Watermolenlaan',
            'huisnummer' => 1,
            'huisnummerToevoeging' => 'bis',
            'huisletter' => 'A',
            'toevoegingAdres' => '3e etage',
            'postcode' => '3447GT',
            'postbusnummer' => 123,
            'plaats' => 'Woerden',
            'straatHuisnummer' => 'Watermolenlaan 1',
            'postcodeWoonplaats' => '3447GT Woerden',
            'regio' => 'Utrecht',
            'land' => 'Nederland',
        ];

        $address = Address::fromArray($data);

        self::assertSame('correspondentieadres', $address->type);
        self::assertSame('Ja', $address->shielded);
        self::assertSame('Watermolenlaan 1 3447GT Woerden', $address->fullAddress);
        self::assertSame('Watermolenlaan', $address->streetName);
        self::assertSame(1, $address->houseNumber);
        self::assertSame('bis', $address->houseNumberAddition);
        self::assertSame('A', $address->houseLetter);
        self::assertSame('3e etage', $address->addressAddition);
        self::assertSame('3447GT', $address->postalCode);
        self::assertSame(123, $address->poBoxNumber);
        self::assertSame('Woerden', $address->city);
        self::assertSame('Watermolenlaan 1', $address->streetHouseNumber);
        self::assertSame('3447GT Woerden', $address->postalCodeCity);
        self::assertSame('Utrecht', $address->region);
        self::assertSame('Nederland', $address->country);
    }

    public function test_from_array_parses_nested_geo_data(): void
    {
        $data = [
            'type' => 'bezoekadres',
            'geoData' => [
                'addresseerbaarObjectId' => '0632010000010090',
                'nummerAanduidingId' => '0632200000010090',
                'gpsLatitude' => 52.08151653230184,
                'gpsLongitude' => 4.890048011859921,
                'rijksdriehoekX' => 120921.45,
                'rijksdriehoekY' => 454921.47,
                'rijksdriehoekZ' => 0.0,
            ],
        ];

        $address = Address::fromArray($data);

        self::assertInstanceOf(GeoData::class, $address->geoData);
        self::assertSame('0632010000010090', $address->geoData->addressableObjectId);
        self::assertSame(52.08151653230184, $address->geoData->gpsLatitude);
    }

    public function test_from_array_returns_null_geo_data_when_absent(): void
    {
        $data = ['type' => 'bezoekadres'];

        $address = Address::fromArray($data);

        self::assertNull($address->geoData);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $address = Address::fake();

        self::assertSame('bezoekadres', $address->type);
        self::assertSame('Nee', $address->shielded);
        self::assertSame('Watermolenlaan 1 3447GT Woerden', $address->fullAddress);
        self::assertSame('Watermolenlaan', $address->streetName);
        self::assertSame(1, $address->houseNumber);
        self::assertSame('3447GT', $address->postalCode);
        self::assertSame('Woerden', $address->city);
        self::assertSame('Nederland', $address->country);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $address = Address::fake();

        $array = $address->toArray();

        self::assertSame('bezoekadres', $array['type']);
        self::assertSame('Nee', $array['indicatieAfgeschermd']);
        self::assertSame('Watermolenlaan 1 3447GT Woerden', $array['volledigAdres']);
        self::assertSame('Watermolenlaan', $array['straatnaam']);
        self::assertSame(1, $array['huisnummer']);
        self::assertSame('3447GT', $array['postcode']);
        self::assertSame('Woerden', $array['plaats']);
        self::assertSame('Nederland', $array['land']);
        self::assertNull($array['geoData']);
    }

    public function test_to_array_includes_nested_geo_data(): void
    {
        $address = Address::fake(geoData: GeoData::fake());

        $array = $address->toArray();

        self::assertIsArray($array['geoData']);
        self::assertSame('0632010000010090', $array['geoData']['addresseerbaarObjectId']);
    }
}
