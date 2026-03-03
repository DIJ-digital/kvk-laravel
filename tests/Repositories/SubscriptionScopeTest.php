<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\SubscriptionScope;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SubscriptionScopeTest extends TestCase
{
    public function test_signals_calls_gateway_and_returns_result(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'aantal' => 100,
                'totaal' => 1,
                'totaalPaginas' => 1,
                'signalen' => [
                    [
                        'id' => 'signal-001',
                        'timestamp' => '2024-05-14T15:25:13.773Z',
                        'kvknummer' => '69792917',
                        'signaalType' => 'SignaalGewijzigdeInschrijving',
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/abonnementen/subscription-456', []])
            ->willReturn($apiResponse);

        $scope = new SubscriptionScope($gateway, 'subscription-456');
        $result = $scope->signals();

        self::assertInstanceOf(SignalsResult::class, $result);
        self::assertSame(1, $result->page);
    }

    public function test_signals_passes_query_params(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 2,
                'aantal' => 50,
                'totaal' => 100,
                'totaalPaginas' => 2,
                'signalen' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', [
                'api/v1/abonnementen/subscription-456',
                [
                    'vanaf' => '2024-01-01T00:00:00Z',
                    'tot' => '2024-12-31T23:59:59Z',
                    'pagina' => 2,
                    'aantal' => 50,
                ],
            ])
            ->willReturn($apiResponse);

        $scope = new SubscriptionScope($gateway, 'subscription-456');
        $result = $scope
            ->from('2024-01-01T00:00:00Z')
            ->to('2024-12-31T23:59:59Z')
            ->page(2)
            ->resultsPerPage(50)
            ->signals();

        self::assertInstanceOf(SignalsResult::class, $result);
    }

    public function test_signal_calls_gateway_and_returns_response(): void
    {
        $apiResponse = new Response(
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

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/abonnementen/subscription-456/signalen/signal-001'])
            ->willReturn($apiResponse);

        $scope = new SubscriptionScope($gateway, 'subscription-456');
        $result = $scope->signal('signal-001');

        self::assertInstanceOf(SignalResponse::class, $result);
        self::assertSame('3e96fad5-606e-43be-9bd5-4718f8afd273', $result->messageId);
    }

    public function test_fluent_setters_return_self(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $scope = new SubscriptionScope($gateway, 'subscription-456');

        self::assertSame($scope, $scope->from('2024-01-01T00:00:00Z'));
        self::assertSame($scope, $scope->to('2024-12-31T23:59:59Z'));
        self::assertSame($scope, $scope->page(1));
        self::assertSame($scope, $scope->resultsPerPage(100));
    }
}
