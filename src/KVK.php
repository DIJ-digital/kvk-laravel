<?php

declare(strict_types=1);

namespace DIJ\Kvk;

use DIJ\Kvk\Repositories\BaseProfileRepository;
use DIJ\Kvk\Repositories\BranchProfileRepository;
use DIJ\Kvk\Repositories\NamingRepository;
use DIJ\Kvk\Repositories\SearchRepository;
use DIJ\Kvk\Repositories\SubscriptionRepository;

class KVK
{
    public function __construct(
        protected KVKGateway $gateway,
    ) {}

    public function search(): SearchRepository
    {
        return new SearchRepository($this->gateway);
    }

    public function baseProfile(string $kvkNumber): BaseProfileRepository
    {
        return new BaseProfileRepository($this->gateway, $kvkNumber);
    }

    public function branchProfile(string $branchNumber): BranchProfileRepository
    {
        return new BranchProfileRepository($this->gateway, $branchNumber);
    }

    public function naming(string $kvkNumber): NamingRepository
    {
        return new NamingRepository($this->gateway, $kvkNumber);
    }

    public function subscriptions(): SubscriptionRepository
    {
        return new SubscriptionRepository($this->gateway);
    }
}
