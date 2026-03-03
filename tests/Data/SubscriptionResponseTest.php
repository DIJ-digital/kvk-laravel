<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\SubscriptionContract;
use DIJ\Kvk\Data\Responses\SubscriptionResponse;
use PHPUnit\Framework\TestCase;

final class SubscriptionResponseTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $response = SubscriptionResponse::fromArray([
            'id' => 'subscription-456',
            'contract' => ['id' => 'contract-789'],
            'startDatum' => '2024-01-01T00:00:00Z',
            'actief' => true,
        ]);

        self::assertSame('subscription-456', $response->id);
        self::assertInstanceOf(SubscriptionContract::class, $response->contract);
        self::assertSame('contract-789', $response->contract->id);
        self::assertSame('2024-01-01T00:00:00Z', $response->startDate);
        self::assertTrue($response->active);
        self::assertNull($response->endDate);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $response = SubscriptionResponse::fromArray([
            'id' => 'subscription-456',
            'contract' => ['id' => 'contract-789'],
            'startDatum' => '2024-01-01T00:00:00Z',
            'actief' => true,
            'eindDatum' => '2025-12-31T23:59:59Z',
        ]);

        self::assertSame('2025-12-31T23:59:59Z', $response->endDate);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $response = SubscriptionResponse::fake();

        self::assertSame('subscription-456', $response->id);
        self::assertSame('contract-789', $response->contract->id);
        self::assertSame('2024-01-01T00:00:00Z', $response->startDate);
        self::assertTrue($response->active);
        self::assertNull($response->endDate);
    }
}
