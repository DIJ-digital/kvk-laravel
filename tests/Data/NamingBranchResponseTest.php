<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\NamingBranchResponse;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Data\ValueObjects\TradeName;
use PHPUnit\Framework\TestCase;

final class NamingBranchResponseTest extends TestCase
{
    public function test_from_array_maps_commercial_branch_fields(): void
    {
        $response = NamingBranchResponse::fromArray([
            'vestigingsnummer' => '000037178598',
            'eersteHandelsnaam' => 'Test Stichting Bolderbast',
            'handelsnamen' => [['naam' => 'Test Stichting Bolderbast', 'volgorde' => 1]],
            'links' => [['rel' => 'self', 'href' => 'https://api.kvk.nl/api/v1/naamgevingen/kvknummer/69599068']],
        ]);

        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('Test Stichting Bolderbast', $response->firstTradeName);
        self::assertNull($response->name);
        self::assertNull($response->alsoKnownAs);
        self::assertCount(1, $response->tradeNames);
        self::assertInstanceOf(TradeName::class, $response->tradeNames[0]);
        self::assertCount(1, $response->links);
        self::assertInstanceOf(Link::class, $response->links[0]);
    }

    public function test_from_array_maps_non_commercial_branch_fields(): void
    {
        $response = NamingBranchResponse::fromArray([
            'vestigingsnummer' => '000037178599',
            'naam' => 'Stichting Branch',
            'ookGenoemd' => 'Branch Alias',
        ]);

        self::assertSame('000037178599', $response->branchNumber);
        self::assertNull($response->firstTradeName);
        self::assertSame('Stichting Branch', $response->name);
        self::assertSame('Branch Alias', $response->alsoKnownAs);
        self::assertSame([], $response->tradeNames);
        self::assertSame([], $response->links);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $response = NamingBranchResponse::fake();

        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('Test Stichting Bolderbast', $response->firstTradeName);
        self::assertCount(1, $response->tradeNames);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $response = NamingBranchResponse::fake();

        $array = $response->toArray();

        self::assertSame('000037178598', $array['vestigingsnummer']);
        self::assertSame('Test Stichting Bolderbast', $array['eersteHandelsnaam']);
        self::assertNull($array['naam']);
        self::assertNull($array['ookGenoemd']);
        self::assertCount(1, $array['handelsnamen']);
        self::assertSame([], $array['links']);
    }
}
