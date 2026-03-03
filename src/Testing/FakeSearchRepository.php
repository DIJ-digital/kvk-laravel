<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Data\Parameters\SearchParameters;
use DIJ\Kvk\Data\Results\SearchResult;

final readonly class FakeSearchRepository
{
    public function __construct(
        private SearchResult $result,
    ) {}

    public function kvkNumber(string $kvkNumber): self
    {
        return $this;
    }

    public function rsin(string $RSIN): self
    {
        return $this;
    }

    public function branchNumber(string $branchNumber): self
    {
        return $this;
    }

    public function name(string $name): self
    {
        return $this;
    }

    public function streetName(string $streetName): self
    {
        return $this;
    }

    public function city(string $city): self
    {
        return $this;
    }

    public function postalCode(string $postalCode): self
    {
        return $this;
    }

    public function houseNumber(int $houseNumber): self
    {
        return $this;
    }

    public function houseLetter(string $houseLetter): self
    {
        return $this;
    }

    public function poBoxNumber(int $poBoxNumber): self
    {
        return $this;
    }

    /**
     * @param  array<'hoofdvestiging'|'nevenvestiging'|'rechtspersoon'>  $type
     */
    public function type(array $type): self
    {
        return $this;
    }

    public function includeInactiveRegistrations(bool $includeInactiveRegistrations = true): self
    {
        return $this;
    }

    public function page(int $page): self
    {
        return $this;
    }

    public function resultsPerPage(int $resultsPerPage): self
    {
        return $this;
    }

    public function get(): SearchResult
    {
        return $this->result;
    }

    public function search(SearchParameters $parameters): SearchResult
    {
        return $this->result;
    }
}
