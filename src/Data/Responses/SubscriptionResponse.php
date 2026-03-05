<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SubscriptionResponse implements Arrayable
{
    public function __construct(
        public string $id,
        public SubscriptionContract $contract,
        public string $startDate,
        public bool $active,
        public ?string $endDate = null,
    ) {}

    /**
     * @param  array{id: string, contract: array{id: string}, startDatum: string, actief: bool, eindDatum?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            contract: SubscriptionContract::fromArray($data['contract']),
            startDate: $data['startDatum'],
            active: $data['actief'],
            endDate: $data['eindDatum'] ?? null,
        );
    }

    public static function fake(
        string $id = 'subscription-456',
        ?SubscriptionContract $contract = null,
        string $startDate = '2024-01-01T00:00:00Z',
        bool $active = true,
        ?string $endDate = null,
    ): self {
        $contract ??= SubscriptionContract::fake();

        return new self(
            id: $id,
            contract: $contract,
            startDate: $startDate,
            active: $active,
            endDate: $endDate,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contract' => $this->contract->toArray(),
            'startDatum' => $this->startDate,
            'actief' => $this->active,
            'eindDatum' => $this->endDate,
        ];
    }
}
