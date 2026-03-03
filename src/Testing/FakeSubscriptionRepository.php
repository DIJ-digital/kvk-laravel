<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Data\Results\SubscriptionsResult;

final readonly class FakeSubscriptionRepository
{
    public function __construct(
        private SubscriptionsResult $result,
        private SignalsResult $signalsResult,
        private SignalResponse $signalResponse,
    ) {}

    public function get(): SubscriptionsResult
    {
        return $this->result;
    }

    public function subscription(string $subscriptionId): FakeSubscriptionScope
    {
        return new FakeSubscriptionScope($this->signalsResult, $this->signalResponse);
    }
}
