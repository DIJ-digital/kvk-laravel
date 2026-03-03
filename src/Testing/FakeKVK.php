<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Collections\SearchResponseCollection;
use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\Data\Results\SearchResult;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Data\Results\SubscriptionsResult;

final class FakeKVK
{
    private SearchResult $searchResult;

    private BaseProfileResponse $baseProfileResponse;

    private BaseProfileOwnerResponse $baseProfileOwnerResponse;

    private BaseProfileMainBranchResponse $baseProfileMainBranchResponse;

    private BaseProfileBranchesResult $baseProfileBranchesResult;

    private BranchProfileResponse $branchProfileResponse;

    private NamingResponse $namingResponse;

    private SubscriptionsResult $subscriptionsResult;

    private SignalsResult $signalsResult;

    private SignalResponse $signalResponse;

    public function __construct(SearchResponse ...$responses)
    {
        $this->searchResult = new SearchResult(
            items: new SearchResponseCollection(array_values($responses)),
            page: 1,
            resultsPerPage: 10,
            total: count($responses),
        );
        $this->baseProfileResponse = BaseProfileResponse::fake();
        $this->baseProfileOwnerResponse = BaseProfileOwnerResponse::fake();
        $this->baseProfileMainBranchResponse = BaseProfileMainBranchResponse::fake();
        $this->baseProfileBranchesResult = BaseProfileBranchesResult::fake();
        $this->branchProfileResponse = BranchProfileResponse::fake();
        $this->namingResponse = NamingResponse::fake();
        $this->subscriptionsResult = SubscriptionsResult::fake();
        $this->signalsResult = SignalsResult::fake();
        $this->signalResponse = SignalResponse::fake();
    }

    public function withSearchResponses(SearchResponse ...$responses): self
    {
        $this->searchResult = new SearchResult(
            items: new SearchResponseCollection(array_values($responses)),
            page: 1,
            resultsPerPage: 10,
            total: count($responses),
        );

        return $this;
    }

    public function withBaseProfile(BaseProfileResponse $response): self
    {
        $this->baseProfileResponse = $response;

        return $this;
    }

    public function withBaseProfileOwner(BaseProfileOwnerResponse $response): self
    {
        $this->baseProfileOwnerResponse = $response;

        return $this;
    }

    public function withBaseProfileMainBranch(BaseProfileMainBranchResponse $response): self
    {
        $this->baseProfileMainBranchResponse = $response;

        return $this;
    }

    public function withBaseProfileBranches(BaseProfileBranchesResult $result): self
    {
        $this->baseProfileBranchesResult = $result;

        return $this;
    }

    public function withBranchProfile(BranchProfileResponse $response): self
    {
        $this->branchProfileResponse = $response;

        return $this;
    }

    public function withNaming(NamingResponse $response): self
    {
        $this->namingResponse = $response;

        return $this;
    }

    public function withSubscriptions(SubscriptionsResult $result): self
    {
        $this->subscriptionsResult = $result;

        return $this;
    }

    public function withSignals(SignalsResult $result): self
    {
        $this->signalsResult = $result;

        return $this;
    }

    public function withSignal(SignalResponse $response): self
    {
        $this->signalResponse = $response;

        return $this;
    }

    public function search(): FakeSearchRepository
    {
        return new FakeSearchRepository($this->searchResult);
    }

    public function baseProfile(string $kvkNumber): FakeBaseProfileRepository
    {
        return new FakeBaseProfileRepository(
            $this->baseProfileResponse,
            $this->baseProfileOwnerResponse,
            $this->baseProfileMainBranchResponse,
            $this->baseProfileBranchesResult,
        );
    }

    public function branchProfile(string $branchNumber): FakeBranchProfileRepository
    {
        return new FakeBranchProfileRepository($this->branchProfileResponse);
    }

    public function naming(string $kvkNumber): FakeNamingRepository
    {
        return new FakeNamingRepository($this->namingResponse);
    }

    public function subscriptions(): FakeSubscriptionRepository
    {
        return new FakeSubscriptionRepository(
            $this->subscriptionsResult,
            $this->signalsResult,
            $this->signalResponse,
        );
    }
}
