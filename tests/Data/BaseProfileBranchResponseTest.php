<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\BaseProfileBranchResponse;
use DIJ\Kvk\Data\ValueObjects\Link;
use PHPUnit\Framework\TestCase;

final class BaseProfileBranchResponseTest extends TestCase
{
    public function test_from_array_maps_all_fields(): void
    {
        $data = [
            'vestigingsnummer' => '000037178598',
            'eersteHandelsnaam' => 'Test BV Donald',
            'indHoofdvestiging' => 'Ja',
            'indCommercieleVestiging' => 'Ja',
            'volledigAdres' => 'Hizzaarderlaan 1 1234AB Lollum',
            'links' => [
                ['rel' => 'basisprofiel', 'href' => 'https://api.kvk.nl/api/v1/basisprofielen/69599068'],
            ],
        ];

        $response = BaseProfileBranchResponse::fromArray($data);

        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('Test BV Donald', $response->firstTradeName);
        self::assertSame('Ja', $response->mainBranchIndicator);
        self::assertSame('Ja', $response->commercialBranchIndicator);
        self::assertSame('Hizzaarderlaan 1 1234AB Lollum', $response->fullAddress);
        self::assertCount(1, $response->links);
        self::assertInstanceOf(Link::class, $response->links[0]);
        self::assertSame('basisprofiel', $response->links[0]->rel);
    }

    public function test_from_array_handles_absent_optional_fields(): void
    {
        $data = [
            'vestigingsnummer' => '000037178598',
            'eersteHandelsnaam' => 'Test BV Donald',
            'indHoofdvestiging' => 'Ja',
            'indCommercieleVestiging' => 'Ja',
        ];

        $response = BaseProfileBranchResponse::fromArray($data);

        self::assertNull($response->fullAddress);
        self::assertSame([], $response->links);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $response = BaseProfileBranchResponse::fake();

        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('Test BV Donald', $response->firstTradeName);
        self::assertSame('Ja', $response->mainBranchIndicator);
        self::assertSame('Ja', $response->commercialBranchIndicator);
        self::assertSame('Hizzaarderlaan 1 1234AB Lollum', $response->fullAddress);
    }
}
