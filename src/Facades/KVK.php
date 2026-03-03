<?php

declare(strict_types=1);

namespace DIJ\Kvk\Facades;

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
use DIJ\Kvk\KVK as KVKConcrete;
use DIJ\Kvk\Repositories\BaseProfileRepository;
use DIJ\Kvk\Repositories\BranchProfileRepository;
use DIJ\Kvk\Repositories\NamingRepository;
use DIJ\Kvk\Repositories\SearchRepository;
use DIJ\Kvk\Repositories\SubscriptionRepository;
use DIJ\Kvk\Testing\FakeKVK;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SearchRepository search()
 * @method static BaseProfileRepository baseProfile(string $kvkNumber)
 * @method static BranchProfileRepository branchProfile(string $branchNumber)
 * @method static NamingRepository naming(string $kvkNumber)
 * @method static SubscriptionRepository subscriptions()
 * @method static FakeKVK fake(?BaseProfileResponse $baseProfile = null, ?BaseProfileOwnerResponse $baseProfileOwner = null, ?BaseProfileMainBranchResponse $baseProfileMainBranch = null, ?BaseProfileBranchesResult $baseProfileBranches = null, ?BranchProfileResponse $branchProfile = null, ?NamingResponse $naming = null, ?SubscriptionsResult $subscriptions = null, ?SignalsResult $signals = null, ?SignalResponse $signal = null, SearchResponse ...$searchResponses)
 */
class KVK extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return KVKConcrete::class;
    }

    public static function fake(
        ?BaseProfileResponse $baseProfile = null,
        ?BaseProfileOwnerResponse $baseProfileOwner = null,
        ?BaseProfileMainBranchResponse $baseProfileMainBranch = null,
        ?BaseProfileBranchesResult $baseProfileBranches = null,
        ?BranchProfileResponse $branchProfile = null,
        ?NamingResponse $naming = null,
        ?SubscriptionsResult $subscriptions = null,
        ?SignalsResult $signals = null,
        ?SignalResponse $signal = null,
        SearchResponse ...$searchResponses,
    ): FakeKVK {
        $fake = new FakeKVK(...$searchResponses);

        if ($baseProfile !== null) {
            $fake->withBaseProfile($baseProfile);
        }

        if ($baseProfileOwner !== null) {
            $fake->withBaseProfileOwner($baseProfileOwner);
        }

        if ($baseProfileMainBranch !== null) {
            $fake->withBaseProfileMainBranch($baseProfileMainBranch);
        }

        if ($baseProfileBranches !== null) {
            $fake->withBaseProfileBranches($baseProfileBranches);
        }

        if ($branchProfile !== null) {
            $fake->withBranchProfile($branchProfile);
        }

        if ($naming !== null) {
            $fake->withNaming($naming);
        }

        if ($subscriptions !== null) {
            $fake->withSubscriptions($subscriptions);
        }

        if ($signals !== null) {
            $fake->withSignals($signals);
        }

        if ($signal !== null) {
            $fake->withSignal($signal);
        }

        static::swap($fake);

        return $fake;
    }
}
