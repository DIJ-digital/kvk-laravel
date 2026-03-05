<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SbiActivity implements Arrayable
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sbiCode' => $this->sbiCode,
            'sbiOmschrijving' => $this->sbiDescription,
            'indHoofdactiviteit' => $this->mainActivityIndicator,
        ];
    }
}
