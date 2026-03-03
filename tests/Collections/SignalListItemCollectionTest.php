<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Collections;

use DIJ\Kvk\Collections\SignalListItemCollection;
use DIJ\Kvk\Data\Responses\SignalListItem;
use PHPUnit\Framework\TestCase;

final class SignalListItemCollectionTest extends TestCase
{
    public function test_collection_holds_typed_items(): void
    {
        $item = new SignalListItem(
            id: 'signal-001',
            timestamp: '2024-05-14T15:25:13.773Z',
            kvkNumber: '69792917',
            signalType: 'SignaalGewijzigdeInschrijving',
        );

        $collection = new SignalListItemCollection([$item]);

        self::assertCount(1, $collection);
        self::assertInstanceOf(SignalListItem::class, $collection->first());
        self::assertSame('signal-001', $collection->first()->id);
    }

    public function test_collection_supports_chaining(): void
    {
        $items = [
            new SignalListItem(id: 's-1', timestamp: '2024-05-14T15:25:13.773Z', kvkNumber: '69792917', signalType: 'SignaalGewijzigdeInschrijving'),
            new SignalListItem(id: 's-2', timestamp: '2024-05-14T16:00:00.000Z', kvkNumber: '69599068', signalType: 'SignaalNieuweInschrijving'),
        ];

        $collection = new SignalListItemCollection($items);
        $filtered = $collection->filter(fn (SignalListItem $item): bool => $item->kvkNumber === '69792917');

        self::assertCount(2, $collection);
        self::assertCount(1, $filtered);
        self::assertInstanceOf(SignalListItemCollection::class, $filtered);
    }
}
