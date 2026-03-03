<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Settings;
use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase
{
    public function test_construct_sets_properties(): void
    {
        $settings = new Settings(
            base_url: 'https://api.kvk.nl',
            api_key: 'test-api-key',
        );

        self::assertSame('https://api.kvk.nl', $settings->base_url);
        self::assertSame('test-api-key', $settings->api_key);
    }

    public function test_from_array_maps_values(): void
    {
        $settings = Settings::fromArray([
            'base_url' => 'https://api.kvk.nl/test',
            'api_key' => 'another-key',
        ]);

        self::assertSame('https://api.kvk.nl/test', $settings->base_url);
        self::assertSame('another-key', $settings->api_key);
    }
}
