<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

readonly class SbiActivity
{
    public function __construct(
        public string $sbiCode,
        public string $sbiDescription,
        public string $mainActivityIndicator,
    ) {}

    /**
     * @param  array{sbiCode: string, sbiOmschrijving: string, indHoofdactiviteit: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sbiCode: $data['sbiCode'],
            sbiDescription: $data['sbiOmschrijving'],
            mainActivityIndicator: $data['indHoofdactiviteit'],
        );
    }

    public static function fake(
        string $sbiCode = '86101',
        string $sbiDescription = 'Universitair medisch centra',
        string $mainActivityIndicator = 'Ja',
    ): self {
        return new self(
            sbiCode: $sbiCode,
            sbiDescription: $sbiDescription,
            mainActivityIndicator: $mainActivityIndicator,
        );
    }
}
