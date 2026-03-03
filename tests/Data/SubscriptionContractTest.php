<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\SubscriptionContract;
use PHPUnit\Framework\TestCase;

final class SubscriptionContractTest extends TestCase
{
    public function test_from_array_maps_fields(): void
    {
        $contract = SubscriptionContract::fromArray([
            'id' => 'contract-789',
        ]);

        self::assertSame('contract-789', $contract->id);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $contract = SubscriptionContract::fake();

        self::assertSame('contract-789', $contract->id);
    }
}
