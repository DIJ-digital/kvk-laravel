<?php

declare(strict_types=1);

namespace DIJ\Kvk\Repositories;

use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\KVKGateway;

class SubscriptionRepository
{
    public function __construct(
        protected KVKGateway $gateway,
    ) {}

    public function get(): SubscriptionsResult
    {
        $result = $this->gateway->get('api/v1/abonnementen');

        return SubscriptionsResult::fromResponse($result);
    }

    public function subscription(string $subscriptionId): SubscriptionScope
    {
        return new SubscriptionScope($this->gateway, $subscriptionId);
    }
}
