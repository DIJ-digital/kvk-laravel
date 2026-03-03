<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Collections\SubscriptionCollection;
use DIJ\Kvk\Data\Responses\SubscriptionResponse;
use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SubscriptionsResultTest extends TestCase
{
    public function test_from_array_maps_fields(): void
    {
        $result = SubscriptionsResult::fromArray([
            'klantId' => 'customer-123',
            'abonnementen' => [
                [
                    'id' => 'subscription-456',
                    'contract' => ['id' => 'contract-789'],
                    'startDatum' => '2024-01-01T00:00:00Z',
                    'actief' => true,
                ],
            ],
        ]);

        self::assertSame('customer-123', $result->customerId);
        self::assertInstanceOf(SubscriptionCollection::class, $result->subscriptions);
        self::assertCount(1, $result->subscriptions);
        self::assertInstanceOf(SubscriptionResponse::class, $result->subscriptions[0]);
        self::assertSame('subscription-456', $result->subscriptions[0]->id);
    }

    public function test_from_response_parses_valid_json(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'klantId' => 'customer-123',
                'abonnementen' => [
                    [
                        'id' => 'subscription-456',
                        'contract' => ['id' => 'contract-789'],
                        'startDatum' => '2024-01-01T00:00:00Z',
                        'actief' => true,
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $result = SubscriptionsResult::fromResponse($httpResponse);

        self::assertSame('customer-123', $result->customerId);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $httpResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode('not-an-array', JSON_THROW_ON_ERROR)),
        );

        $this->expectException(KvkException::class);

        SubscriptionsResult::fromResponse($httpResponse);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $result = SubscriptionsResult::fake();

        self::assertSame('customer-123', $result->customerId);
        self::assertCount(1, $result->subscriptions);
    }
}
