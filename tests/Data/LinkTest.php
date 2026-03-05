<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\Link;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    public function test_constructor(): void
    {
        $link = new Link(
            rel: 'basisprofiel',
            href: 'https://api.kvk.nl/api/v1/basisprofielen/69599068',
        );

        self::assertSame('basisprofiel', $link->rel);
        self::assertSame('https://api.kvk.nl/api/v1/basisprofielen/69599068', $link->href);
    }

    public function test_from_array(): void
    {
        $link = Link::fromArray([
            'rel' => 'vestigingsprofiel',
            'href' => 'https://api.kvk.nl/api/v1/vestigingsprofielen/000037178598',
        ]);

        self::assertSame('vestigingsprofiel', $link->rel);
        self::assertSame('https://api.kvk.nl/api/v1/vestigingsprofielen/000037178598', $link->href);
    }

    public function test_to_array_maps_keys(): void
    {
        $link = new Link(
            rel: 'basisprofiel',
            href: 'https://api.kvk.nl/api/v1/basisprofielen/69599068',
        );

        $array = $link->toArray();

        self::assertSame('basisprofiel', $array['rel']);
        self::assertSame('https://api.kvk.nl/api/v1/basisprofielen/69599068', $array['href']);
    }
}
