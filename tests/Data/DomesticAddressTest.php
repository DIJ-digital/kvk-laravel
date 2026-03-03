<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\DomesticAddress;
use PHPUnit\Framework\TestCase;

final class DomesticAddressTest extends TestCase
{
    public function test_constructor_with_all_fields(): void
    {
        $address = new DomesticAddress(
            type: 'bezoekadres',
            streetName: 'Hizzaarderlaan',
            houseNumber: 1,
            houseLetter: 'A',
            poBoxNumber: 123,
            postalCode: '1234AB',
            city: 'Lollum',
        );

        self::assertSame('bezoekadres', $address->type);
        self::assertSame('Hizzaarderlaan', $address->streetName);
        self::assertSame(1, $address->houseNumber);
        self::assertSame('A', $address->houseLetter);
        self::assertSame(123, $address->poBoxNumber);
        self::assertSame('1234AB', $address->postalCode);
        self::assertSame('Lollum', $address->city);
    }

    public function test_from_array_with_all_fields(): void
    {
        $address = DomesticAddress::fromArray([
            'type' => 'bezoekadres',
            'straatnaam' => 'Hizzaarderlaan',
            'huisnummer' => 1,
            'huisletter' => 'A',
            'postbusnummer' => 123,
            'postcode' => '1234AB',
            'plaats' => 'Lollum',
        ]);

        self::assertSame('bezoekadres', $address->type);
        self::assertSame('Hizzaarderlaan', $address->streetName);
        self::assertSame(1, $address->houseNumber);
        self::assertSame('A', $address->houseLetter);
        self::assertSame(123, $address->poBoxNumber);
        self::assertSame('1234AB', $address->postalCode);
        self::assertSame('Lollum', $address->city);
    }

    public function test_from_array_with_minimal_fields(): void
    {
        $address = DomesticAddress::fromArray([
            'type' => 'correspondentieadres',
        ]);

        self::assertSame('correspondentieadres', $address->type);
        self::assertNull($address->streetName);
        self::assertNull($address->houseNumber);
        self::assertNull($address->houseLetter);
        self::assertNull($address->poBoxNumber);
        self::assertNull($address->postalCode);
        self::assertNull($address->city);
    }
}
