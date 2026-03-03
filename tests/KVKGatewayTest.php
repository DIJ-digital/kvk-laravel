<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests;

use DIJ\Kvk\Data\Settings;
use DIJ\Kvk\Exceptions\KvkAuthenticationException;
use DIJ\Kvk\Exceptions\KvkRequestException;
use DIJ\Kvk\Exceptions\KvkServerException;
use DIJ\Kvk\KVKGateway;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class KVKGatewayTest extends TestCase
{
    public function test_construct_creates_client_with_base_url_and_api_key_header(): void
    {
        $settings = new Settings(
            base_url: 'https://api.kvk.nl',
            api_key: 'secret-key',
        );

        $pendingRequest = $this->createMock(PendingRequest::class);
        $pendingRequest->expects(self::once())
            ->method('withHeader')
            ->with('apikey', 'secret-key')
            ->willReturnSelf();

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::once())
            ->method('__call')
            ->with('baseUrl', ['https://api.kvk.nl'])
            ->willReturn($pendingRequest);

        new KVKGateway($factory, $settings);
    }

    public function test_call_delegates_method_to_pending_request(): void
    {
        $settings = new Settings(
            base_url: 'https://api.kvk.nl',
            api_key: 'secret-key',
        );

        $pendingRequest = $this->createMock(PendingRequest::class);
        $pendingRequest->method('withHeader')->willReturnSelf();
        $pendingRequest->expects(self::once())
            ->method('get')
            ->with('api/v2/zoeken', ['kvkNummer' => '69599068'])
            ->willReturn('result');

        $factory = $this->createStub(Factory::class);
        $factory->method('__call')->willReturn($pendingRequest);

        $gateway = new KVKGateway($factory, $settings);

        $result = $gateway->__call('get', ['api/v2/zoeken', ['kvkNummer' => '69599068']]);

        self::assertSame('result', $result);
    }

    public function test_call_passes_through_successful_response(): void
    {
        $response = new Response(new Psr7Response(200, [], '{"ok": true}'));

        $gateway = $this->createGatewayReturning($response);
        $result = $gateway->__call('get', ['api/v2/zoeken', []]);

        self::assertSame($response, $result);
    }

    public function test_call_throws_authentication_exception_on_401(): void
    {
        $gateway = $this->createGatewayReturning(
            new Response(new Psr7Response(401, [], 'Unauthorized')),
        );

        try {
            $gateway->__call('get', ['api/v2/zoeken', []]);
            self::fail('Expected KvkAuthenticationException');
        } catch (KvkAuthenticationException $e) {
            self::assertSame(401, $e->statusCode);
            self::assertSame('Unauthorized', $e->responseBody);
            self::assertSame('KVK API authentication failed (HTTP 401)', $e->getMessage());
        }
    }

    public function test_call_throws_authentication_exception_on_403(): void
    {
        $gateway = $this->createGatewayReturning(
            new Response(new Psr7Response(403, [], 'Forbidden')),
        );

        try {
            $gateway->__call('get', ['api/v2/zoeken', []]);
            self::fail('Expected KvkAuthenticationException');
        } catch (KvkAuthenticationException $e) {
            self::assertSame(403, $e->statusCode);
            self::assertSame('Forbidden', $e->responseBody);
            self::assertSame('KVK API authentication failed (HTTP 403)', $e->getMessage());
        }
    }

    public function test_call_throws_request_exception_on_400(): void
    {
        $gateway = $this->createGatewayReturning(
            new Response(new Psr7Response(400, [], 'Bad Request')),
        );

        try {
            $gateway->__call('get', ['api/v2/zoeken', []]);
            self::fail('Expected KvkRequestException');
        } catch (KvkRequestException $e) {
            self::assertSame(400, $e->statusCode);
            self::assertSame('Bad Request', $e->responseBody);
            self::assertSame('KVK API request failed (HTTP 400)', $e->getMessage());
        }
    }

    public function test_call_throws_request_exception_on_404(): void
    {
        $gateway = $this->createGatewayReturning(
            new Response(new Psr7Response(404, [], 'Not Found')),
        );

        try {
            $gateway->__call('get', ['api/v2/zoeken', []]);
            self::fail('Expected KvkRequestException');
        } catch (KvkRequestException $e) {
            self::assertSame(404, $e->statusCode);
            self::assertSame('Not Found', $e->responseBody);
            self::assertSame('KVK API request failed (HTTP 404)', $e->getMessage());
        }
    }

    public function test_call_throws_server_exception_on_500(): void
    {
        $gateway = $this->createGatewayReturning(
            new Response(new Psr7Response(500, [], 'Internal Server Error')),
        );

        try {
            $gateway->__call('get', ['api/v2/zoeken', []]);
            self::fail('Expected KvkServerException');
        } catch (KvkServerException $e) {
            self::assertSame(500, $e->statusCode);
            self::assertSame('Internal Server Error', $e->responseBody);
            self::assertSame('KVK API server error (HTTP 500)', $e->getMessage());
        }
    }

    public function test_call_throws_server_exception_on_503(): void
    {
        $gateway = $this->createGatewayReturning(
            new Response(new Psr7Response(503, [], 'Service Unavailable')),
        );

        try {
            $gateway->__call('get', ['api/v2/zoeken', []]);
            self::fail('Expected KvkServerException');
        } catch (KvkServerException $e) {
            self::assertSame(503, $e->statusCode);
            self::assertSame('Service Unavailable', $e->responseBody);
        }
    }

    private function createGatewayReturning(Response $response): KVKGateway
    {
        $settings = new Settings(base_url: 'https://api.kvk.nl', api_key: 'secret-key');

        $pendingRequest = $this->createStub(PendingRequest::class);
        $pendingRequest->method('withHeader')->willReturnSelf();
        $pendingRequest->method('get')->willReturn($response);

        $factory = $this->createStub(Factory::class);
        $factory->method('__call')->willReturn($pendingRequest);

        return new KVKGateway($factory, $settings);
    }
}
