<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Results;

use DIJ\Kvk\Collections\SubscriptionCollection;
use DIJ\Kvk\Data\Responses\SubscriptionResponse;
use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class SubscriptionsResult implements Arrayable
{
    public function __construct(
        public string $customerId,
        public SubscriptionCollection $subscriptions,
    ) {}

    /**
     * @param  array{klantId: string, abonnementen: list<array{id: string, contract: array{id: string}, startDatum: string, actief: bool, eindDatum?: string|null}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['klantId'],
            subscriptions: new SubscriptionCollection(
                array_map(
                    SubscriptionResponse::fromArray(...),
                    $data['abonnementen'],
                ),
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

        /** @var array{klantId: string, abonnementen: list<array{id: string, contract: array{id: string}, startDatum: string, actief: bool, eindDatum?: string|null}>} $body */
        return self::fromArray($body);
    }

    public static function fake(string $customerId = 'customer-123', ?SubscriptionCollection $subscriptions = null): self
    {
        $subscriptions ??= new SubscriptionCollection([SubscriptionResponse::fake()]);

        return new self(
            customerId: $customerId,
            subscriptions: $subscriptions,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'klantId' => $this->customerId,
            'abonnementen' => $this->subscriptions->toArray(),
        ];
    }
}
