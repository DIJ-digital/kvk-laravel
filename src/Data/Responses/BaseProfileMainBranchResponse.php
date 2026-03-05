<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Address;
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
readonly class BaseProfileMainBranchResponse implements Arrayable
{
    /**
     * @param  list<TradeName>  $tradeNames
     * @param  list<Address>  $addresses
     * @param  list<string>  $websites
     * @param  list<SbiActivity>  $sbiActivities
     * @param  list<Link>  $links
     */
    public function __construct(
        public string $branchNumber,
        public string $kvkNumber,
        public ?string $nonMailingIndicator = null,
        public string $firstTradeName = '',
        public string $mainBranchIndicator = '',
        public string $commercialBranchIndicator = '',
        public ?string $rsin = null,
        public ?string $formalRegistrationDate = null,
        public ?MaterialRegistration $materialRegistration = null,
        public ?int $fullTimeEmployees = null,
        public ?int $totalEmployees = null,
        public ?int $partTimeEmployees = null,
        public array $tradeNames = [],
        public array $addresses = [],
        public array $websites = [],
        public array $sbiActivities = [],
        public array $links = [],
    ) {}

    /**
     * @param  array{vestigingsnummer: string, kvkNummer: string, indNonMailing?: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, rsin?: string, formeleRegistratiedatum?: string, materieleRegistratie?: array{datumAanvang?: string, datumEinde?: string|null}, voltijdWerkzamePersonen?: int, totaalWerkzamePersonen?: int, deeltijdWerkzamePersonen?: int, handelsnamen?: list<array{naam: string, volgorde: int}>, adressen?: list<array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}>, websites?: list<string>, sbiActiviteiten?: list<array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}>, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            branchNumber: $data['vestigingsnummer'],
            kvkNumber: $data['kvkNummer'],
            nonMailingIndicator: $data['indNonMailing'] ?? null,
            firstTradeName: $data['eersteHandelsnaam'],
            mainBranchIndicator: $data['indHoofdvestiging'],
            commercialBranchIndicator: $data['indCommercieleVestiging'],
            rsin: $data['rsin'] ?? null,
            formalRegistrationDate: $data['formeleRegistratiedatum'] ?? null,
            materialRegistration: isset($data['materieleRegistratie'])
                ? MaterialRegistration::fromArray($data['materieleRegistratie'])
                : null,
            fullTimeEmployees: $data['voltijdWerkzamePersonen'] ?? null,
            totalEmployees: $data['totaalWerkzamePersonen'] ?? null,
            partTimeEmployees: $data['deeltijdWerkzamePersonen'] ?? null,
            tradeNames: array_map(
                TradeName::fromArray(...),
                $data['handelsnamen'] ?? [],
            ),
            addresses: array_map(
                Address::fromArray(...),
                $data['adressen'] ?? [],
            ),
            websites: $data['websites'] ?? [],
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

        /** @var array{vestigingsnummer: string, kvkNummer: string, indNonMailing: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, rsin?: string, formeleRegistratiedatum?: string, materieleRegistratie?: array{datumAanvang?: string, datumEinde?: string|null}, voltijdWerkzamePersonen?: int, totaalWerkzamePersonen?: int, deeltijdWerkzamePersonen?: int, handelsnamen?: list<array{naam: string, volgorde: int}>, adressen?: list<array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}>, websites?: list<string>, sbiActiviteiten?: list<array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}>, links?: list<array{rel: string, href: string}>} $body */
        return self::fromArray($body);
    }

    /**
     * @param  list<TradeName>  $tradeNames
     * @param  list<Address>  $addresses
     * @param  list<string>  $websites
     * @param  list<SbiActivity>  $sbiActivities
     * @param  list<Link>  $links
     */
    public static function fake(
        string $branchNumber = '000037178598',
        string $kvkNumber = '69599068',
        ?string $nonMailingIndicator = 'Nee',
        string $firstTradeName = 'Test BV Donald',
        string $mainBranchIndicator = 'Ja',
        string $commercialBranchIndicator = 'Ja',
        ?string $rsin = '123456789',
        ?string $formalRegistrationDate = '20150622',
        ?MaterialRegistration $materialRegistration = null,
        ?int $fullTimeEmployees = 10,
        ?int $totalEmployees = 15,
        ?int $partTimeEmployees = 5,
        array $tradeNames = [],
        array $addresses = [],
        array $websites = [],
        array $sbiActivities = [],
        array $links = [],
    ): self {
        $materialRegistration ??= MaterialRegistration::fake();

        return new self(
            branchNumber: $branchNumber,
            kvkNumber: $kvkNumber,
            nonMailingIndicator: $nonMailingIndicator,
            firstTradeName: $firstTradeName,
            mainBranchIndicator: $mainBranchIndicator,
            commercialBranchIndicator: $commercialBranchIndicator,
            rsin: $rsin,
            formalRegistrationDate: $formalRegistrationDate,
            materialRegistration: $materialRegistration,
            fullTimeEmployees: $fullTimeEmployees,
            totalEmployees: $totalEmployees,
            partTimeEmployees: $partTimeEmployees,
            tradeNames: $tradeNames === [] ? [TradeName::fake()] : $tradeNames,
            addresses: $addresses === [] ? [Address::fake()] : $addresses,
            websites: $websites === [] ? ['https://example.com'] : $websites,
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
            'vestigingsnummer' => $this->branchNumber,
            'kvkNummer' => $this->kvkNumber,
            'indNonMailing' => $this->nonMailingIndicator,
            'eersteHandelsnaam' => $this->firstTradeName,
            'indHoofdvestiging' => $this->mainBranchIndicator,
            'indCommercieleVestiging' => $this->commercialBranchIndicator,
            'rsin' => $this->rsin,
            'formeleRegistratiedatum' => $this->formalRegistrationDate,
            'materieleRegistratie' => $this->materialRegistration?->toArray(),
            'voltijdWerkzamePersonen' => $this->fullTimeEmployees,
            'totaalWerkzamePersonen' => $this->totalEmployees,
            'deeltijdWerkzamePersonen' => $this->partTimeEmployees,
            'handelsnamen' => array_map(fn (TradeName $tradeName): array => $tradeName->toArray(), $this->tradeNames),
            'adressen' => array_map(fn (Address $address): array => $address->toArray(), $this->addresses),
            'websites' => $this->websites,
            'sbiActiviteiten' => array_map(fn (SbiActivity $activity): array => $activity->toArray(), $this->sbiActivities),
            'links' => array_map(fn (Link $link): array => $link->toArray(), $this->links),
        ];
    }
}
