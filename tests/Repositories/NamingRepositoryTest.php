<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\NamingRepository;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class NamingRepositoryTest extends TestCase
{
    public function test_get_calls_gateway_and_returns_response(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'statutaireNaam' => 'Stichting Bolderbast',
                'naam' => 'Test Stichting Bolderbast',
                'vestigingen' => [
                    [
                        'vestigingsnummer' => '000037178598',
                        'eersteHandelsnaam' => 'Test Stichting Bolderbast',
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/naamgevingen/kvknummer/69599068'])
            ->willReturn($apiResponse);

        $repository = new NamingRepository($gateway, '69599068');
        $result = $repository->get();

        self::assertInstanceOf(NamingResponse::class, $result);
        self::assertSame('69599068', $result->kvkNumber);
    }
}
