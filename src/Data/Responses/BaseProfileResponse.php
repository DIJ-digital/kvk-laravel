<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Data\ValueObjects\MaterialRegistration;
use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use DIJ\Kvk\Data\ValueObjects\TradeName;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class BaseProfileResponse implements Arrayable
{
    /**
     * @param  list<TradeName>  $tradeNames
     * @param  list<SbiActivity>  $sbiActivities
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $kvkNumber,
        public string $nonMailingIndicator,
        public string $name,
        public ?string $formalRegistrationDate = null,
        public ?MaterialRegistration $materialRegistration = null,
        public ?string $statutoryName = null,
        public array $tradeNames = [],
        public array $sbiActivities = [],
        public array $links = [],
    ) {}

    /**
     * @param  array{kvkNummer: string, indNonMailing: string, naam: string, formeleRegistratiedatum?: string, materieleRegistratie?: array{datumAanvang?: string, datumEinde?: string|null}, statutaireNaam?: string, handelsnamen?: list<array{naam: string, volgorde: int}>, sbiActiviteiten?: list<array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}>, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            kvkNumber: $data['kvkNummer'],
            nonMailingIndicator: $data['indNonMailing'],
            name: $data['naam'],
            formalRegistrationDate: $data['formeleRegistratiedatum'] ?? null,
            materialRegistration: isset($data['materieleRegistratie'])
                ? MaterialRegistration::fromArray($data['materieleRegistratie'])
                : null,
            statutoryName: $data['statutaireNaam'] ?? null,
            tradeNames: array_map(
                TradeName::fromArray(...),
                $data['handelsnamen'] ?? [],
            ),
            sbiActivities: array_map(
                SbiActivity::fromArray(...),
                $data['sbiActiviteiten'] ?? [],
            ),
            links: array_map(
                Link::fromArray(...),
                $data['links'] ?? [],
            ),
        );
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        if (! is_array($body)) {
            throw new KvkException(
                'KVK API returned an invalid response body',
                $response->status(),
                $response->body(),
            );
        }

        /** @var array{kvkNummer: string, indNonMailing: string, naam: string, formeleRegistratiedatum?: string, materieleRegistratie?: array{datumAanvang?: string, datumEinde?: string|null}, statutaireNaam?: string, handelsnamen?: list<array{naam: string, volgorde: int}>, sbiActiviteiten?: list<array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}>, links?: list<array{rel: string, href: string}>} $body */
        return self::fromArray($body);
    }

    /**
     * @param  list<TradeName>  $tradeNames
     * @param  list<SbiActivity>  $sbiActivities
     * @param  list<Link>  $links
     */
    public static function fake(
        string $kvkNumber = '69599068',
        string $nonMailingIndicator = 'Ja',
        string $name = 'Test Stichting Bolderbast',
        ?string $formalRegistrationDate = '20150622',
        ?MaterialRegistration $materialRegistration = null,
        ?string $statutoryName = 'Stichting Bolderbast',
        array $tradeNames = [],
        array $sbiActivities = [],
        array $links = [],
    ): self {
        $materialRegistration ??= MaterialRegistration::fake();

        return new self(
            kvkNumber: $kvkNumber,
            nonMailingIndicator: $nonMailingIndicator,
            name: $name,
            formalRegistrationDate: $formalRegistrationDate,
            materialRegistration: $materialRegistration,
            statutoryName: $statutoryName,
            tradeNames: $tradeNames === [] ? [TradeName::fake()] : $tradeNames,
            sbiActivities: $sbiActivities === [] ? [SbiActivity::fake()] : $sbiActivities,
            links: $links,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'kvkNummer' => $this->kvkNumber,
            'indNonMailing' => $this->nonMailingIndicator,
            'naam' => $this->name,
            'formeleRegistratiedatum' => $this->formalRegistrationDate,
            'materieleRegistratie' => $this->materialRegistration?->toArray(),
            'statutaireNaam' => $this->statutoryName,
            'handelsnamen' => array_map(fn (TradeName $tradeName): array => $tradeName->toArray(), $this->tradeNames),
            'sbiActiviteiten' => array_map(fn (SbiActivity $activity): array => $activity->toArray(), $this->sbiActivities),
            'links' => array_map(fn (Link $link): array => $link->toArray(), $this->links),
        ];
    }
}
