<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Facades;

use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\Facades\KVK;
use DIJ\Kvk\Testing\FakeBranchProfileRepository;
use DIJ\Kvk\Testing\FakeKVK;
use DIJ\Kvk\Testing\FakeNamingRepository;
use DIJ\Kvk\Testing\FakeSubscriptionRepository;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;

final class KVKFakeTest extends TestCase
{
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        parent::tearDown();
    }

    public function test_fake_swaps_facade_instance(): void
    {
        KVK::fake();

        self::assertInstanceOf(FakeKVK::class, KVK::getFacadeRoot());
    }

    public function test_fake_search_returns_empty_result(): void
    {
        KVK::fake();
        $result = KVK::search()->get();

        self::assertCount(0, $result->items);
        self::assertSame(0, $result->total);
        self::assertSame(1, $result->page);
    }

    public function test_fake_search_returns_configured_responses(): void
    {
        KVK::fake()->withSearchResponses(SearchResponse::fake(kvkNumber: '69599068'));
        $result = KVK::search()->kvkNumber('69599068')->get();

        self::assertCount(1, $result->items);
        self::assertSame('69599068', $result->items[0]->kvkNumber);
    }

    public function test_fake_returns_fake_kvk_instance(): void
    {
        $fake = KVK::fake();

        self::assertInstanceOf(FakeKVK::class, $fake);
    }

    public function test_fake_branch_profile_returns_fake_branch_profile_repository(): void
    {
        KVK::fake();

        self::assertInstanceOf(FakeBranchProfileRepository::class, KVK::branchProfile('000037178598'));
    }

    public function test_fake_naming_returns_fake_naming_repository(): void
    {
        KVK::fake();

        self::assertInstanceOf(FakeNamingRepository::class, KVK::naming('69599068'));
    }

    public function test_fake_subscriptions_returns_fake_mutatieservice_repository(): void
    {
        KVK::fake();

        self::assertInstanceOf(FakeSubscriptionRepository::class, KVK::subscriptions());
    }

    public function test_fake_with_base_profile_named_param(): void
    {
        KVK::fake(baseProfile: BaseProfileResponse::fake(kvkNumber: '99999999'));

        self::assertSame('99999999', KVK::baseProfile('99999999')->get()->kvkNumber);
    }

    public function test_fake_with_branch_profile_named_param(): void
    {
        KVK::fake(branchProfile: BranchProfileResponse::fake(kvkNumber: '88888888'));

        self::assertSame('88888888', KVK::branchProfile('000037178598')->get()->kvkNumber);
    }

    public function test_fake_with_naming_named_param(): void
    {
        KVK::fake(naming: NamingResponse::fake(statutoryName: 'Custom Corp'));

        self::assertSame('Custom Corp', KVK::naming('69599068')->get()->statutoryName);
    }

    public function test_fake_with_subscriptions_named_param(): void
    {
        KVK::fake(subscriptions: SubscriptionsResult::fake(customerId: 'custom-customer'));

        self::assertSame('custom-customer', KVK::subscriptions()->get()->customerId);
    }

    public function test_fake_with_signal_named_param(): void
    {
        KVK::fake(signal: SignalResponse::fake(messageId: 'custom-message-id'));

        self::assertSame('custom-message-id', KVK::subscriptions()->subscription('sub-1')->signal('sig-1')->messageId);
    }

    public function test_fake_backward_compat_variadic_search(): void
    {
        KVK::fake()->withSearchResponses(SearchResponse::fake(kvkNumber: '12345678'));
        $result = KVK::search()->get();

        self::assertCount(1, $result->items);
        self::assertSame('12345678', $result->items[0]->kvkNumber);
    }

    public function test_fake_zero_args_returns_defaults(): void
    {
        KVK::fake();

        self::assertSame('69599068', KVK::baseProfile('69599068')->get()->kvkNumber);
        self::assertSame('000037178598', KVK::branchProfile('000037178598')->get()->branchNumber);
        self::assertSame('Stichting Bolderbast', KVK::naming('69599068')->get()->statutoryName);
        self::assertSame('customer-123', KVK::subscriptions()->get()->customerId);
    }

    public function test_fake_with_base_profile_owner_named_param(): void
    {
        KVK::fake(baseProfileOwner: BaseProfileOwnerResponse::fake(legalForm: 'Stichting'));

        self::assertSame('Stichting', KVK::baseProfile('69599068')->owner()->legalForm);
    }

    public function test_fake_with_base_profile_main_branch_named_param(): void
    {
        KVK::fake(baseProfileMainBranch: BaseProfileMainBranchResponse::fake(firstTradeName: 'Custom Main'));

        self::assertSame('Custom Main', KVK::baseProfile('69599068')->mainBranch()->firstTradeName);
    }

    public function test_fake_with_base_profile_branches_named_param(): void
    {
        KVK::fake(baseProfileBranches: BaseProfileBranchesResult::fake(totalBranchCount: 5));

        self::assertSame(5, KVK::baseProfile('69599068')->branches()->totalBranchCount);
    }

    public function test_fake_with_signals_named_param(): void
    {
        KVK::fake(signals: \DIJ\Kvk\Data\Results\SignalsResult::fake(total: 42));

        self::assertSame(42, KVK::subscriptions()->subscription('sub-1')->signals()->total);
    }
}
