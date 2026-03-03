<?php

declare(strict_types=1);

namespace DIJ\Kvk\Repositories;

use DIJ\Kvk\Data\Parameters\SearchParameters;
use DIJ\Kvk\Data\Results\SearchResult;
use DIJ\Kvk\KVKGateway;

class SearchRepository
{
    private ?string $kvkNumber = null;

    private ?string $RSIN = null;

    private ?string $branchNumber = null;

    private ?string $name = null;

    private ?string $streetName = null;

    private ?string $city = null;

    private ?string $postalCode = null;

    private ?int $houseNumber = null;

    private ?string $houseLetter = null;

    private ?int $poBoxNumber = null;

    /** @var array<'hoofdvestiging'|'nevenvestiging'|'rechtspersoon'>|null */
    private ?array $type = null;

    private ?bool $includeInactiveRegistrations = null;

    private int $page = 1;

    private int $resultsPerPage = 100;

    public function __construct(
        protected KVKGateway $gateway,
    ) {}

    public function kvkNumber(string $kvkNumber): self
    {
        $this->kvkNumber = $kvkNumber;

        return $this;
    }

    public function rsin(string $RSIN): self
    {
        $this->RSIN = $RSIN;

        return $this;
    }

    public function branchNumber(string $branchNumber): self
    {
        $this->branchNumber = $branchNumber;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function streetName(string $streetName): self
    {
        $this->streetName = $streetName;

        return $this;
    }

    public function city(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function postalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function houseNumber(int $houseNumber): self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function houseLetter(string $houseLetter): self
    {
        $this->houseLetter = $houseLetter;

        return $this;
    }

    public function poBoxNumber(int $poBoxNumber): self
    {
        $this->poBoxNumber = $poBoxNumber;

        return $this;
    }

    /**
     * @param  array<'hoofdvestiging'|'nevenvestiging'|'rechtspersoon'>  $type
     */
    public function type(array $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function includeInactiveRegistrations(bool $includeInactiveRegistrations = true): self
    {
        $this->includeInactiveRegistrations = $includeInactiveRegistrations;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function resultsPerPage(int $resultsPerPage): self
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }

    public function get(): SearchResult
    {
        return $this->search(new SearchParameters(
            kvkNumber: $this->kvkNumber,
            RSIN: $this->RSIN,
            branchNumber: $this->branchNumber,
            name: $this->name,
            streetName: $this->streetName,
            city: $this->city,
            postalCode: $this->postalCode,
            houseNumber: $this->houseNumber,
            houseLetter: $this->houseLetter,
            poBoxNumber: $this->poBoxNumber,
            type: $this->type,
            includeInactiveRegistrations: $this->includeInactiveRegistrations,
            page: $this->page,
            resultsPerPage: $this->resultsPerPage,
        ));
    }

    public function search(SearchParameters $parameters): SearchResult
    {
        $result = $this->gateway->get('api/v2/zoeken', $parameters->toArray());

        return SearchResult::fromResponse($result);
    }
}
