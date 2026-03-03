<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Data\Responses\BranchProfileResponse;

final readonly class FakeBranchProfileRepository
{
    public function __construct(
        private BranchProfileResponse $response,
    ) {}

    public function geoData(bool $geoData = true): self
    {
        return $this;
    }

    public function get(): BranchProfileResponse
    {
        return $this->response;
    }
}
