<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\SignalListItem;
use PHPUnit\Framework\TestCase;

final class SignalListItemTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $item = SignalListItem::fromArray([
            'id' => 'signal-001',
            'timestamp' => '2024-05-14T15:25:13.773Z',
            'kvknummer' => '69792917',
            'signaalType' => 'SignaalGewijzigdeInschrijving',
        ]);

        self::assertSame('signal-001', $item->id);
        self::assertSame('2024-05-14T15:25:13.773Z', $item->timestamp);
        self::assertSame('69792917', $item->kvkNumber);
        self::assertSame('SignaalGewijzigdeInschrijving', $item->signalType);
        self::assertNull($item->branchNumber);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $item = SignalListItem::fromArray([
            'id' => 'signal-001',
            'timestamp' => '2024-05-14T15:25:13.773Z',
            'kvknummer' => '69792917',
            'signaalType' => 'SignaalGewijzigdeInschrijving',
            'vestigingsnummer' => '000038821281',
        ]);

        self::assertSame('000038821281', $item->branchNumber);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $item = SignalListItem::fake();

        self::assertSame('signal-001', $item->id);
        self::assertSame('2024-05-14T15:25:13.773Z', $item->timestamp);
        self::assertSame('69792917', $item->kvkNumber);
        self::assertSame('SignaalGewijzigdeInschrijving', $item->signalType);
        self::assertSame('000038821281', $item->branchNumber);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $item = SignalListItem::fake();

        $array = $item->toArray();

        self::assertSame('signal-001', $array['id']);
        self::assertSame('2024-05-14T15:25:13.773Z', $array['timestamp']);
        self::assertSame('69792917', $array['kvknummer']);
        self::assertSame('SignaalGewijzigdeInschrijving', $array['signaalType']);
        self::assertSame('000038821281', $array['vestigingsnummer']);
    }
}
