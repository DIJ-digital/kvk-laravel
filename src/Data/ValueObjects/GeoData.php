<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

readonly class GeoData
{
    public function __construct(
        public ?string $addressableObjectId = null,
        public ?string $numberIndicationId = null,
        public ?float $gpsLatitude = null,
        public ?float $gpsLongitude = null,
        public ?float $rijksdriehoekX = null,
        public ?float $rijksdriehoekY = null,
        public ?float $rijksdriehoekZ = null,
    ) {}

    /**
     * @param  array{addresseerbaarObjectId?: string, nummerAanduidingId?: string, gpsLatitude?: float, gpsLongitude?: float, rijksdriehoekX?: float, rijksdriehoekY?: float, rijksdriehoekZ?: float}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            addressableObjectId: $data['addresseerbaarObjectId'] ?? null,
            numberIndicationId: $data['nummerAanduidingId'] ?? null,
            gpsLatitude: $data['gpsLatitude'] ?? null,
            gpsLongitude: $data['gpsLongitude'] ?? null,
            rijksdriehoekX: $data['rijksdriehoekX'] ?? null,
            rijksdriehoekY: $data['rijksdriehoekY'] ?? null,
            rijksdriehoekZ: $data['rijksdriehoekZ'] ?? null,
        );
    }

    public static function fake(
        ?string $addressableObjectId = '0632010000010090',
        ?string $numberIndicationId = '0632200000010090',
        ?float $gpsLatitude = 52.08151653230184,
        ?float $gpsLongitude = 4.890048011859921,
        ?float $rijksdriehoekX = 120921.45,
        ?float $rijksdriehoekY = 454921.47,
        ?float $rijksdriehoekZ = 0.0,
    ): self {
        return new self(
            addressableObjectId: $addressableObjectId,
            numberIndicationId: $numberIndicationId,
            gpsLatitude: $gpsLatitude,
            gpsLongitude: $gpsLongitude,
            rijksdriehoekX: $rijksdriehoekX,
            rijksdriehoekY: $rijksdriehoekY,
            rijksdriehoekZ: $rijksdriehoekZ,
        );
    }
}
