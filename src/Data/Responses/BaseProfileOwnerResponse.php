<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Data\ValueObjects\Address;
use DIJ\Kvk\Data\ValueObjects\Link;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Http\Client\Response;

readonly class BaseProfileOwnerResponse
{
    /**
     * @param  list<Address>  $addresses
     * @param  list<string>  $websites
     * @param  list<Link>  $links
     */
    public function __construct(
        public ?string $rsin = null,
        public ?string $legalForm = null,
        public ?string $extendedLegalForm = null,
        public array $addresses = [],
        public array $websites = [],
        public array $links = [],
    ) {}

    /**
     * @param  array{rsin?: string, rechtsvorm?: string, uitgebreideRechtsvorm?: string, adressen?: list<array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}>, websites?: list<string>, links?: list<array{rel: string, href: string}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rsin: $data['rsin'] ?? null,
            legalForm: $data['rechtsvorm'] ?? null,
            extendedLegalForm: $data['uitgebreideRechtsvorm'] ?? null,
            addresses: array_map(
                Address::fromArray(...),
                $data['adressen'] ?? [],
            ),
            websites: $data['websites'] ?? [],
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

        /** @var array{rsin?: string, rechtsvorm?: string, uitgebreideRechtsvorm?: string, adressen?: list<array{type: string, indicatieAfgeschermd?: string, volledigAdres?: string, straatnaam?: string, huisnummer?: int, huisnummerToevoeging?: string, huisletter?: string, toevoegingAdres?: string, postcode?: string, postbusnummer?: int, plaats?: string, straatHuisnummer?: string, postcodeWoonplaats?: string, regio?: string, land?: string, geoData?: array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}}>, websites?: list<string>, links?: list<array{rel: string, href: string}>} $body */
        return self::fromArray($body);
    }

    /**
     * @param  list<Address>  $addresses
     * @param  list<string>  $websites
     * @param  list<Link>  $links
     */
    public static function fake(
        ?string $rsin = '123456789',
        ?string $legalForm = 'BesloteVennootschap',
        ?string $extendedLegalForm = 'Besloten Vennootschap met gewone structuur',
        array $addresses = [],
        array $websites = [],
        array $links = [],
    ): self {
        return new self(
            rsin: $rsin,
            legalForm: $legalForm,
            extendedLegalForm: $extendedLegalForm,
            addresses: $addresses === [] ? [Address::fake()] : $addresses,
            websites: $websites === [] ? ['https://example.com'] : $websites,
            links: $links,
        );
    }
}
