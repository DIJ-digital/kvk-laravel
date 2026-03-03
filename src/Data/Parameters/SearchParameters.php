<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Parameters;

readonly class SearchParameters
{
    /**
     * @param  array<'hoofdvestiging'|'nevenvestiging'|'rechtspersoon'>|null  $type
     */
    public function __construct(
        public ?string $kvkNumber = null,
        public ?string $RSIN = null,
        public ?string $branchNumber = null,
        public ?string $name = null,
        public ?string $streetName = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?int $houseNumber = null,
        public ?string $houseLetter = null,
        public ?int $poBoxNumber = null,
        public ?array $type = null,
        public ?bool $includeInactiveRegistrations = null,
        public ?int $page = 1,
        public ?int $resultsPerPage = 100,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'kvkNummer' => $this->kvkNumber,
            'rsin' => $this->RSIN,
            'vestigingsnummer' => $this->branchNumber,
            'naam' => $this->name,
            'straatnaam' => $this->streetName,
            'plaats' => $this->city,
            'postcode' => $this->postalCode,
            'huisnummer' => $this->houseNumber,
            'huisletter' => $this->houseLetter,
            'postbusnummer' => $this->poBoxNumber,
            'type' => $this->type,
            'inclusiefInactieveRegistraties' => $this->includeInactiveRegistrations,
            'pagina' => $this->page,
            'resultatenPerPagina' => $this->resultsPerPage,
        ], fn ($value) => $value !== null);
    }
}
