<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SignalResponseTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $response = SignalResponse::fromArray([
            'berichtId' => '3e96fad5-606e-43be-9bd5-4718f8afd273',
            'signaalType' => 'SignaalGewijzigdeInschrijving',
            'registratieId' => '-64945f8e:18f77b51fa3:-4654',
            'registratieTijdstip' => '2024-05-14T15:25:13.773Z',
        ]);

        self::assertSame('3e96fad5-606e-43be-9bd5-4718f8afd273', $response->messageId);
        self::assertSame('SignaalGewijzigdeInschrijving', $response->signalType);
        self::assertSame('-64945f8e:18f77b51fa3:-4654', $response->registrationId);
        self::assertSame('2024-05-14T15:25:13.773Z', $response->registrationTimestamp);
        self::assertSame([], $response->relatesTo);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $response = SignalResponse::fromArray([
            'berichtId' => '3e96fad5-606e-43be-9bd5-4718f8afd273',
            'signaalType' => 'SignaalGewijzigdeInschrijving',
            'registratieId' => '-64945f8e:18f77b51fa3:-4654',
            'registratieTijdstip' => '2024-05-14T15:25:13.773Z',
            'heeftBetrekkingOp' => ['kvkNummer' => '69792917', 'nonMailing' => true],
        ]);

        self::assertSame('69792917', $response->relatesTo['kvkNummer']);
        self::assertTrue($response->relatesTo['nonMailing']);
    }

    public function test_from_response_unwraps_signaal_key(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'signaal' => [
                    'berichtId' => '3e96fad5-606e-43be-9bd5-4718f8afd273',
                    'signaalType' => 'SignaalGewijzigdeInschrijving',
                    'registratieId' => '-64945f8e:18f77b51fa3:-4654',
                    'registratieTijdstip' => '2024-05-14T15:25:13.773Z',
                    'heeftBetrekkingOp' => ['kvkNummer' => '69792917'],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $result = SignalResponse::fromResponse($httpResponse);

        self::assertSame('3e96fad5-606e-43be-9bd5-4718f8afd273', $result->messageId);
        self::assertSame('69792917', $result->relatesTo['kvkNummer']);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode('not-an-array', JSON_THROW_ON_ERROR)),
        );

        $this->expectException(KvkException::class);

        SignalResponse::fromResponse($httpResponse);
    }

    public function test_from_response_throws_on_missing_signaal_key(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'other' => 'data',
            ], JSON_THROW_ON_ERROR)),
        );

        $this->expectException(KvkException::class);

        SignalResponse::fromResponse($httpResponse);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $response = SignalResponse::fake();

        self::assertSame('3e96fad5-606e-43be-9bd5-4718f8afd273', $response->messageId);
        self::assertSame('SignaalGewijzigdeInschrijving', $response->signalType);
        self::assertSame('-64945f8e:18f77b51fa3:-4654', $response->registrationId);
        self::assertSame('2024-05-14T15:25:13.773Z', $response->registrationTimestamp);
        self::assertSame('69792917', $response->relatesTo['kvkNummer']);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $response = SignalResponse::fake();

        $array = $response->toArray();

        self::assertSame('3e96fad5-606e-43be-9bd5-4718f8afd273', $array['berichtId']);
        self::assertSame('SignaalGewijzigdeInschrijving', $array['signaalType']);
        self::assertSame('-64945f8e:18f77b51fa3:-4654', $array['registratieId']);
        self::assertSame('2024-05-14T15:25:13.773Z', $array['registratieTijdstip']);
        self::assertSame('69792917', $array['heeftBetrekkingOp']['kvkNummer']);
    }
}
