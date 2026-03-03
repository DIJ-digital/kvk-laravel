<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Address;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Data\ValueObjects\MaterialRegistration;
use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use DIJ\Kvk\Data\ValueObjects\TradeName;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Http\Client\Response;

readonly class BranchProfileResponse
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
        public string $nonMailingIndicator,
        public string $firstTradeName,
        public string $mainBranchIndicator,
        public string $commercialBranchIndicator,
        public ?string $rsin = null,
        public ?string $formalRegistrationDate = null,
        public ?MaterialRegistration $materialRegistration = null,
        public ?string $statutoryName = null,
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
     * @param  array{vestigingsnummer: string, kvkNummer: string, indNonMailing: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, rsin?: string, formeleRegistratiedatum?: string, materieleRegistratie?: array{datumAanvang?: string, datumEinde?: string|null}, statutaireNaam?: string, voltijdWerkzamePersonen?: int, totaalWerkzamePersonen?: int, deeltijdWerkzamePersonen?: int, handelsnamen?: list<array{naam: string, volgorde: int}>, adressen?: list<array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}>, websites?: list<string>, sbiActiviteiten?: list<array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}>, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            branchNumber: $data['vestigingsnummer'],
            kvkNumber: $data['kvkNummer'],
            nonMailingIndicator: $data['indNonMailing'],
            firstTradeName: $data['eersteHandelsnaam'],
            mainBranchIndicator: $data['indHoofdvestiging'],
            commercialBranchIndicator: $data['indCommercieleVestiging'],
            rsin: $data['rsin'] ?? null,
            formalRegistrationDate: $data['formeleRegistratiedatum'] ?? null,
            materialRegistration: isset($data['materieleRegistratie'])
                ? MaterialRegistration::fromArray($data['materieleRegistratie'])
                : null,
            statutoryName: $data['statutaireNaam'] ?? null,
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

        /** @var array{vestigingsnummer: string, kvkNummer: string, indNonMailing: string, eersteHandelsnaam: string, indHoofdvestiging: string, indCommercieleVestiging: string, rsin?: string, formeleRegistratiedatum?: string, materieleRegistratie?: array{datumAanvang?: string, datumEinde?: string|null}, statutaireNaam?: string, voltijdWerkzamePersonen?: int, totaalWerkzamePersonen?: int, deeltijdWerkzamePersonen?: int, handelsnamen?: list<array{naam: string, volgorde: int}>, adressen?: list<array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}>, websites?: list<string>, sbiActiviteiten?: list<array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}>, links?: list<array{rel: string, href: string}>} $body */
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
        string $kvkNumber = '68750110',
        string $nonMailingIndicator = 'Nee',
        string $firstTradeName = 'Test BV Donald',
        string $mainBranchIndicator = 'Ja',
        string $commercialBranchIndicator = 'Ja',
        ?string $rsin = '123456789',
        ?string $formalRegistrationDate = '20150622',
        ?MaterialRegistration $materialRegistration = null,
        ?string $statutoryName = 'Test BV Donald',
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
            statutoryName: $statutoryName,
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
}
