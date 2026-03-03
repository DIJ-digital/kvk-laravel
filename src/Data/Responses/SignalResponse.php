<?php

declare(strict_types=1);

namespace DIJ\Kvk\Data\Responses;

use DIJ\Kvk\Exceptions\KvkException;
use Illuminate\Http\Client\Response;

readonly class SignalResponse
{
    /**
     * @param  array<string, mixed>  $relatesTo
     */
    public function __construct(
        public string $messageId,
        public string $signalType,
        public string $registrationId,
        public string $registrationTimestamp,
        public array $relatesTo = [],
    ) {}

    /**
     * @param  array{berichtId: string, signaalType: string, registratieId: string, registratieTijdstip: string, heeftBetrekkingOp?: array<string, mixed>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            messageId: $data['berichtId'],
            signalType: $data['signaalType'],
            registrationId: $data['registratieId'],
            registrationTimestamp: $data['registratieTijdstip'],
            relatesTo: $data['heeftBetrekkingOp'] ?? [],
        );
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        if (! is_array($body) || ! is_array($body['signaal'] ?? null)) {
            throw new KvkException(
                'KVK API returned an invalid response body',
                $response->status(),
                $response->body(),
            );
        }

        /** @var array{signaal: array{berichtId: string, signaalType: string, registratieId: string, registratieTijdstip: string, heeftBetrekkingOp?: array<string, mixed>}} $body */
        return self::fromArray($body['signaal']);
    }

    /**
     * @param  array<string, mixed>  $relatesTo
     */
    public static function fake(
        string $messageId = '3e96fad5-606e-43be-9bd5-4718f8afd273',
        string $signalType = 'SignaalGewijzigdeInschrijving',
        string $registrationId = '-64945f8e:18f77b51fa3:-4654',
        string $registrationTimestamp = '2024-05-14T15:25:13.773Z',
        array $relatesTo = [],
    ): self {
        return new self(
            messageId: $messageId,
            signalType: $signalType,
            registrationId: $registrationId,
            registrationTimestamp: $registrationTimestamp,
            relatesTo: $relatesTo === [] ? ['kvkNummer' => '69792917'] : $relatesTo,
        );
    }
}
