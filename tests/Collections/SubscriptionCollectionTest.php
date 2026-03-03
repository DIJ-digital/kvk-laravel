<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Collections;

use DIJ\Kvk\Collections\SubscriptionCollection;
use DIJ\Kvk\Data\Responses\SubscriptionContract;
use DIJ\Kvk\Data\Responses\SubscriptionResponse;
use PHPUnit\Framework\TestCase;

final class SubscriptionCollectionTest extends TestCase
{
    public function test_collection_holds_typed_items(): void
    {
        $item = new SubscriptionResponse(
            id: 'subscription-456',
            contract: new SubscriptionContract(id: 'contract-789'),
            startDate: '2024-01-01T00:00:00Z',
            active: true,
        );

        $collection = new SubscriptionCollection([$item]);

        self::assertCount(1, $collection);
        self::assertInstanceOf(SubscriptionResponse::class, $collection->first());
        self::assertSame('subscription-456', $collection->first()->id);
    }

    public function test_collection_supports_chaining(): void
    {
        $items = [
            new SubscriptionResponse(id: 'sub-1', contract: new SubscriptionContract(id: 'c-1'), startDate: '2024-01-01T00:00:00Z', active: true),
            new SubscriptionResponse(id: 'sub-2', contract: new SubscriptionContract(id: 'c-2'), startDate: '2024-01-01T00:00:00Z', active: false),
        ];

        $collection = new SubscriptionCollection($items);
        $filtered = $collection->filter(fn (SubscriptionResponse $item): bool => $item->active);

        self::assertCount(2, $collection);
        self::assertCount(1, $filtered);
        self::assertInstanceOf(SubscriptionCollection::class, $filtered);
    }
}
