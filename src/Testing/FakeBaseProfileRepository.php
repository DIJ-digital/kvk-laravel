<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;

final readonly class FakeBaseProfileRepository
{
    public function __construct(
        private BaseProfileResponse $getResponse,
        private BaseProfileOwnerResponse $ownerResponse,
        private BaseProfileMainBranchResponse $mainBranchResponse,
        private BaseProfileBranchesResult $branchesResult,
    ) {}

    public function geoData(bool $geoData = true): self
    {
        return $this;
    }

    public function get(): BaseProfileResponse
    {
        return $this->getResponse;
    }

    public function owner(): BaseProfileOwnerResponse
    {
        return $this->ownerResponse;
    }

    public function mainBranch(): BaseProfileMainBranchResponse
    {
        return $this->mainBranchResponse;
    }

    public function branches(): BaseProfileBranchesResult
    {
        return $this->branchesResult;
    }
}
