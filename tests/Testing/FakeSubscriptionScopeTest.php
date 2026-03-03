<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Testing\FakeSubscriptionScope;
use PHPUnit\Framework\TestCase;

final class FakeSubscriptionScopeTest extends TestCase
{
    public function test_signals_returns_signals_result(): void
    {
        $scope = new FakeSubscriptionScope(SignalsResult::fake(), SignalResponse::fake());
        $result = $scope->signals();
        self::assertInstanceOf(SignalsResult::class, $result);
    }

    public function test_signal_returns_signal_response(): void
    {
        $scope = new FakeSubscriptionScope(SignalsResult::fake(), SignalResponse::fake());
        $result = $scope->signal('signal-001');
        self::assertInstanceOf(SignalResponse::class, $result);
    }

    public function test_fluent_setters_return_self(): void
    {
        $scope = new FakeSubscriptionScope(SignalsResult::fake(), SignalResponse::fake());

        self::assertSame($scope, $scope->from('2024-01-01T00:00:00Z'));
        self::assertSame($scope, $scope->to('2024-12-31T23:59:59Z'));
        self::assertSame($scope, $scope->page(1));
        self::assertSame($scope, $scope->resultsPerPage(100));
    }
}
