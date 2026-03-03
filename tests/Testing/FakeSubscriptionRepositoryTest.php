<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\Testing\FakeSubscriptionRepository;
use DIJ\Kvk\Testing\FakeSubscriptionScope;
use PHPUnit\Framework\TestCase;

final class FakeSubscriptionRepositoryTest extends TestCase
{
    public function test_get_returns_subscriptions_result(): void
    {
        $repo = new FakeSubscriptionRepository(SubscriptionsResult::fake(), SignalsResult::fake(), SignalResponse::fake());
        $result = $repo->get();
        self::assertInstanceOf(SubscriptionsResult::class, $result);
    }

    public function test_subscription_returns_fake_scope(): void
    {
        $repo = new FakeSubscriptionRepository(SubscriptionsResult::fake(), SignalsResult::fake(), SignalResponse::fake());
        $scope = $repo->subscription('subscription-456');
        self::assertInstanceOf(FakeSubscriptionScope::class, $scope);
    }
}
