<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\ForeignAddress;
use PHPUnit\Framework\TestCase;

final class ForeignAddressTest extends TestCase
{
    public function test_constructor_with_all_fields(): void
    {
        $address = new ForeignAddress(
            streetHouseNumber: '123 Main Street',
            postalCodeCity: '12345 Berlin',
            country: 'Duitsland',
        );

        self::assertSame('123 Main Street', $address->streetHouseNumber);
        self::assertSame('12345 Berlin', $address->postalCodeCity);
        self::assertSame('Duitsland', $address->country);
    }

    public function test_from_array_with_all_fields(): void
    {
        $address = ForeignAddress::fromArray([
            'straatHuisnummer' => '123 Main Street',
            'postcodeWoonplaats' => '12345 Berlin',
            'land' => 'Duitsland',
        ]);

        self::assertSame('123 Main Street', $address->streetHouseNumber);
        self::assertSame('12345 Berlin', $address->postalCodeCity);
        self::assertSame('Duitsland', $address->country);
    }

    public function test_from_array_with_empty_data(): void
    {
        $address = ForeignAddress::fromArray([]);

        self::assertNull($address->streetHouseNumber);
        self::assertNull($address->postalCodeCity);
        self::assertNull($address->country);
    }
}
