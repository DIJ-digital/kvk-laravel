<?php

declare(strict_types=1);

namespace DIJ\Kvk\Repositories;

use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\KVKGateway;

class BranchProfileRepository
{
    private bool $geoData = false;

    public function __construct(
        protected KVKGateway $gateway,
        private readonly string $branchNumber,
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

    public function get(): BranchProfileResponse
    {
        $result = $this->gateway->get("api/v1/vestigingsprofielen/{$this->branchNumber}", $this->queryParams());

        return BranchProfileResponse::fromResponse($result);
    }
}
