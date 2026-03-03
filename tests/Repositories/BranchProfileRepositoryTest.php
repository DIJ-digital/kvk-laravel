<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\BranchProfileRepository;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class BranchProfileRepositoryTest extends TestCase
{
    public function test_get_calls_gateway_and_returns_response(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'vestigingsnummer' => '000037178598',
                'kvkNummer' => '68750110',
                'indNonMailing' => 'Nee',
                'eersteHandelsnaam' => 'Test BV Donald',
                'indHoofdvestiging' => 'Ja',
                'indCommercieleVestiging' => 'Ja',
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/vestigingsprofielen/000037178598', []])
            ->willReturn($apiResponse);

        $repository = new BranchProfileRepository($gateway, '000037178598');
        $result = $repository->get();

        self::assertInstanceOf(BranchProfileResponse::class, $result);
        self::assertSame('000037178598', $result->branchNumber);
    }

    public function test_geo_data_passes_parameter_to_gateway(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'vestigingsnummer' => '000037178598',
                'kvkNummer' => '68750110',
                'indNonMailing' => 'Nee',
                'eersteHandelsnaam' => 'Test BV Donald',
                'indHoofdvestiging' => 'Ja',
                'indCommercieleVestiging' => 'Ja',
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/vestigingsprofielen/000037178598', ['geoData' => true]])
            ->willReturn($apiResponse);

        $repository = new BranchProfileRepository($gateway, '000037178598');
        $repository->geoData()->get();
    }

    public function test_geo_data_with_false_passes_empty_query_params(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'vestigingsnummer' => '000037178598',
                'kvkNummer' => '68750110',
                'indNonMailing' => 'Nee',
                'eersteHandelsnaam' => 'Test BV Donald',
                'indHoofdvestiging' => 'Ja',
                'indCommercieleVestiging' => 'Ja',
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/vestigingsprofielen/000037178598', []])
            ->willReturn($apiResponse);

        $repository = new BranchProfileRepository($gateway, '000037178598');
        $repository->geoData(false)->get();
    }

    public function test_geo_data_returns_self(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $repository = new BranchProfileRepository($gateway, '000037178598');

        self::assertSame($repository, $repository->geoData());
    }
}
