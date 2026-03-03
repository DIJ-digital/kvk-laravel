<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

readonly class SignalListItem
{
    public function __construct(
        public string $id,
        public string $timestamp,
        public string $kvkNumber,
        public string $signalType,
        public ?string $branchNumber = null,
    ) {}

    /**
     * @param  array{id: string, timestamp: string, kvknummer: string, signaalType: string, vestigingsnummer?: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            timestamp: $data['timestamp'],
            kvkNumber: $data['kvknummer'],
            signalType: $data['signaalType'],
            branchNumber: $data['vestigingsnummer'] ?? null,
        );
    }

    public static function fake(
        string $id = 'signal-001',
        string $timestamp = '2024-05-14T15:25:13.773Z',
        string $kvkNumber = '69792917',
        string $signalType = 'SignaalGewijzigdeInschrijving',
        ?string $branchNumber = '000038821281',
    ): self {
        return new self(
            id: $id,
            timestamp: $timestamp,
            kvkNumber: $kvkNumber,
            signalType: $signalType,
            branchNumber: $branchNumber,
        );
    }
}
