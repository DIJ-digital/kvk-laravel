<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests;

use DIJ\Kvk\KVK;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\BaseProfileRepository;
use DIJ\Kvk\Repositories\BranchProfileRepository;
use DIJ\Kvk\Repositories\NamingRepository;
use DIJ\Kvk\Repositories\SearchRepository;
use DIJ\Kvk\Repositories\SubscriptionRepository;
use PHPUnit\Framework\TestCase;

final class KVKTest extends TestCase
{
    public function test_search_returns_search_repository(): void
    {
        $gateway = $this->createStub(KVKGateway::class);

        $kvk = new KVK($gateway);
        $repository = $kvk->search();

        self::assertInstanceOf(SearchRepository::class, $repository);
    }

    public function test_base_profile_returns_base_profile_repository(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $kvk = new KVK($gateway);
        $repository = $kvk->baseProfile('69599068');
        self::assertInstanceOf(BaseProfileRepository::class, $repository);
    }

    public function test_branch_profile_returns_branch_profile_repository(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $kvk = new KVK($gateway);
        $repository = $kvk->branchProfile('000037178598');

        self::assertInstanceOf(BranchProfileRepository::class, $repository);
    }

    public function test_naming_returns_naming_repository(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $kvk = new KVK($gateway);
        $repository = $kvk->naming('69599068');

        self::assertInstanceOf(NamingRepository::class, $repository);
    }

    public function test_subscriptions_returns_mutatieservice_repository(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $kvk = new KVK($gateway);
        $repository = $kvk->subscriptions();

        self::assertInstanceOf(SubscriptionRepository::class, $repository);
    }
}
