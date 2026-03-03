<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\GeoData;
use PHPUnit\Framework\TestCase;

final class GeoDataTest extends TestCase
{
    public function test_from_array_maps_all_fields(): void
    {
        $data = [
            'addresseerbaarObjectId' => '0632010000010090',
            'nummerAanduidingId' => '0632200000010090',
            'gpsLatitude' => 52.08151653230184,
            'gpsLongitude' => 4.890048011859921,
            'rijksdriehoekX' => 120921.45,
            'rijksdriehoekY' => 454921.47,
            'rijksdriehoekZ' => 0.0,
        ];

        $geoData = GeoData::fromArray($data);

        self::assertSame('0632010000010090', $geoData->addressableObjectId);
        self::assertSame('0632200000010090', $geoData->numberIndicationId);
        self::assertSame(52.08151653230184, $geoData->gpsLatitude);
        self::assertSame(4.890048011859921, $geoData->gpsLongitude);
        self::assertSame(120921.45, $geoData->rijksdriehoekX);
        self::assertSame(454921.47, $geoData->rijksdriehoekY);
        self::assertSame(0.0, $geoData->rijksdriehoekZ);
    }

    public function test_from_array_handles_absent_fields(): void
    {
        $data = [];

        $geoData = GeoData::fromArray($data);

        self::assertNull($geoData->addressableObjectId);
        self::assertNull($geoData->numberIndicationId);
        self::assertNull($geoData->gpsLatitude);
        self::assertNull($geoData->gpsLongitude);
        self::assertNull($geoData->rijksdriehoekX);
        self::assertNull($geoData->rijksdriehoekY);
        self::assertNull($geoData->rijksdriehoekZ);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $geoData = GeoData::fake();

        self::assertSame('0632010000010090', $geoData->addressableObjectId);
        self::assertSame('0632200000010090', $geoData->numberIndicationId);
        self::assertSame(52.08151653230184, $geoData->gpsLatitude);
        self::assertSame(4.890048011859921, $geoData->gpsLongitude);
        self::assertSame(120921.45, $geoData->rijksdriehoekX);
        self::assertSame(454921.47, $geoData->rijksdriehoekY);
        self::assertSame(0.0, $geoData->rijksdriehoekZ);
    }
}
