<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\TradeName;
use PHPUnit\Framework\TestCase;

final class TradeNameTest extends TestCase
{
    public function test_from_array_returns_instance_with_correct_properties(): void
    {
        $tradeName = TradeName::fromArray([
            'naam' => 'Test BV Donald',
            'volgorde' => 1,
        ]);

        self::assertSame('Test BV Donald', $tradeName->name);
        self::assertSame(1, $tradeName->order);
    }

    public function test_from_array_with_different_values(): void
    {
        $tradeName = TradeName::fromArray([
            'naam' => 'Acme Corporation',
            'volgorde' => 3,
        ]);

        self::assertSame('Acme Corporation', $tradeName->name);
        self::assertSame(3, $tradeName->order);
    }

    public function test_fake_returns_instance_with_correct_defaults(): void
    {
        $tradeName = TradeName::fake();

        self::assertSame('Test BV Donald', $tradeName->name);
        self::assertSame(1, $tradeName->order);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $tradeName = TradeName::fake();

        $array = $tradeName->toArray();

        self::assertSame('Test BV Donald', $array['naam']);
        self::assertSame(1, $array['volgorde']);
    }
}
