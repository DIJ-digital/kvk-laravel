<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\Testing\FakeBaseProfileRepository;
use DIJ\Kvk\Testing\FakeBranchProfileRepository;
use DIJ\Kvk\Testing\FakeKVK;
use DIJ\Kvk\Testing\FakeNamingRepository;
use DIJ\Kvk\Testing\FakeSearchRepository;
use DIJ\Kvk\Testing\FakeSubscriptionRepository;
use PHPUnit\Framework\TestCase;

final class FakeKVKTest extends TestCase
{
    public function test_search_returns_fake_search_repository(): void
    {
        $fake = new FakeKVK;
        $repository = $fake->search();

        self::assertInstanceOf(FakeSearchRepository::class, $repository);
    }

    public function test_search_returns_empty_result_when_no_responses(): void
    {
        $fake = new FakeKVK;
        $result = $fake->search()->get();

        self::assertCount(0, $result->items);
        self::assertSame(0, $result->total);
        self::assertSame(1, $result->page);
        self::assertSame(10, $result->resultsPerPage);
    }

    public function test_search_returns_configured_responses(): void
    {
        $response1 = SearchResponse::fake(kvkNumber: '69599068');
        $response2 = SearchResponse::fake(kvkNumber: '68750110');

        $fake = new FakeKVK($response1, $response2);
        $result = $fake->search()->get();

        self::assertCount(2, $result->items);
        self::assertSame(2, $result->total);
        self::assertSame('69599068', $result->items[0]->kvkNumber);
        self::assertSame('68750110', $result->items[1]->kvkNumber);
    }

    public function test_search_result_has_correct_pagination(): void
    {
        $response = SearchResponse::fake();
        $fake = new FakeKVK($response);
        $result = $fake->search()->get();

        self::assertSame(1, $result->page);
        self::assertSame(10, $result->resultsPerPage);
        self::assertNull($result->previous);
        self::assertNull($result->next);
    }

    public function test_base_profile_returns_fake_base_profile_repository(): void
    {
        $fake = new FakeKVK;
        $repository = $fake->baseProfile('69599068');
        self::assertInstanceOf(FakeBaseProfileRepository::class, $repository);
    }

    public function test_branch_profile_returns_fake_branch_profile_repository(): void
    {
        $fake = new FakeKVK;
        $repository = $fake->branchProfile('000037178598');

        self::assertInstanceOf(FakeBranchProfileRepository::class, $repository);
    }

    public function test_naming_returns_fake_naming_repository(): void
    {
        $fake = new FakeKVK;
        $repository = $fake->naming('69599068');

        self::assertInstanceOf(FakeNamingRepository::class, $repository);
    }

    public function test_subscriptions_returns_fake_mutatieservice_repository(): void
    {
        $fake = new FakeKVK;
        $repository = $fake->subscriptions();

        self::assertInstanceOf(FakeSubscriptionRepository::class, $repository);
    }

    public function test_with_base_profile_customizes_response(): void
    {
        $custom = BaseProfileResponse::fake(kvkNumber: '99999999');
        $fake = (new FakeKVK)->withBaseProfile($custom);

        self::assertSame('99999999', $fake->baseProfile('99999999')->get()->kvkNumber);
    }

    public function test_with_branch_profile_customizes_response(): void
    {
        $custom = BranchProfileResponse::fake(kvkNumber: '88888888');
        $fake = (new FakeKVK)->withBranchProfile($custom);

        self::assertSame('88888888', $fake->branchProfile('000037178598')->get()->kvkNumber);
    }

    public function test_with_naming_customizes_response(): void
    {
        $custom = NamingResponse::fake(statutoryName: 'Custom Corp');
        $fake = (new FakeKVK)->withNaming($custom);

        self::assertSame('Custom Corp', $fake->naming('69599068')->get()->statutoryName);
    }

    public function test_with_subscriptions_customizes_result(): void
    {
        $custom = SubscriptionsResult::fake(customerId: 'custom-customer');
        $fake = (new FakeKVK)->withSubscriptions($custom);

        self::assertSame('custom-customer', $fake->subscriptions()->get()->customerId);
    }

    public function test_with_signal_customizes_response(): void
    {
        $custom = SignalResponse::fake(messageId: 'custom-message-id');
        $fake = (new FakeKVK)->withSignal($custom);

        self::assertSame('custom-message-id', $fake->subscriptions()->subscription('sub-1')->signal('sig-1')->messageId);
    }

    public function test_with_signals_customizes_result(): void
    {
        $custom = SignalsResult::fake(total: 42);
        $fake = (new FakeKVK)->withSignals($custom);

        self::assertSame(42, $fake->subscriptions()->subscription('sub-1')->signals()->total);
    }

    public function test_with_search_responses_customizes_result(): void
    {
        $fake = (new FakeKVK)->withSearchResponses(
            SearchResponse::fake(kvkNumber: '11111111'),
            SearchResponse::fake(kvkNumber: '22222222'),
        );
        $result = $fake->search()->get();

        self::assertSame(2, $result->total);
        self::assertSame('11111111', $result->items[0]->kvkNumber);
    }

    public function test_base_profile_returns_default_response(): void
    {
        $fake = new FakeKVK;

        self::assertSame('69599068', $fake->baseProfile('69599068')->get()->kvkNumber);
    }

    public function test_with_base_profile_owner_customizes_response(): void
    {
        $custom = BaseProfileOwnerResponse::fake(legalForm: 'Stichting');
        $fake = (new FakeKVK)->withBaseProfileOwner($custom);

        self::assertSame('Stichting', $fake->baseProfile('69599068')->owner()->legalForm);
    }

    public function test_with_base_profile_main_branch_customizes_response(): void
    {
        $custom = BaseProfileMainBranchResponse::fake(firstTradeName: 'Custom Main');
        $fake = (new FakeKVK)->withBaseProfileMainBranch($custom);

        self::assertSame('Custom Main', $fake->baseProfile('69599068')->mainBranch()->firstTradeName);
    }

    public function test_with_base_profile_branches_customizes_result(): void
    {
        $custom = BaseProfileBranchesResult::fake(totalBranchCount: 5);
        $fake = (new FakeKVK)->withBaseProfileBranches($custom);

        self::assertSame(5, $fake->baseProfile('69599068')->branches()->totalBranchCount);
    }
}
