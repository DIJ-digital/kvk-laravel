<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\SubscriptionRepository;
use DIJ\Kvk\Repositories\SubscriptionScope;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SubscriptionRepositoryTest extends TestCase
{
    public function test_get_calls_gateway_and_returns_result(): void
    {
        $apiResponse = new Response(
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

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/abonnementen'])
            ->willReturn($apiResponse);

        $repository = new SubscriptionRepository($gateway);
        $result = $repository->get();

        self::assertInstanceOf(SubscriptionsResult::class, $result);
        self::assertSame('customer-123', $result->customerId);
    }

    public function test_subscription_returns_scope(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $repository = new SubscriptionRepository($gateway);
        $scope = $repository->subscription('subscription-456');

        self::assertInstanceOf(SubscriptionScope::class, $scope);
    }
}
