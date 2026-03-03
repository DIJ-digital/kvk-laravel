<?php

declare(strict_types=1);

namespace DIJ\Kvk\Repositories;

use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\KVKGateway;

class BaseProfileRepository
{
    private bool $geoData = false;

    public function __construct(
        protected KVKGateway $gateway,
        private readonly string $kvkNumber,
    ) {}

    public function geoData(bool $geoData = true): self
    {
        $this->geoData = $geoData;

        return $this;
    }

    /**
     * @return array<string, bool>
     */
    private function queryParams(): array
    {
        if ($this->geoData) {
            return ['geoData' => true];
        }

        return [];
    }

    public function get(): BaseProfileResponse
    {
        $result = $this->gateway->get("api/v1/basisprofielen/{$this->kvkNumber}", $this->queryParams());

        return BaseProfileResponse::fromResponse($result);
    }

    public function owner(): BaseProfileOwnerResponse
    {
        $result = $this->gateway->get("api/v1/basisprofielen/{$this->kvkNumber}/eigenaar", $this->queryParams());

        return BaseProfileOwnerResponse::fromResponse($result);
    }

    public function mainBranch(): BaseProfileMainBranchResponse
    {
        $result = $this->gateway->get("api/v1/basisprofielen/{$this->kvkNumber}/hoofdvestiging", $this->queryParams());

        return BaseProfileMainBranchResponse::fromResponse($result);
    }

    public function branches(): BaseProfileBranchesResult
    {
        $result = $this->gateway->get("api/v1/basisprofielen/{$this->kvkNumber}/vestigingen", $this->queryParams());

        return BaseProfileBranchesResult::fromResponse($result);
    }
}
