<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\BaseProfileRepository;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class BaseProfileRepositoryTest extends TestCase
{
    public function test_get_calls_gateway_and_returns_response(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'indNonMailing' => 'Ja',
                'naam' => 'Test Stichting Bolderbast',
                'formeleRegistratiedatum' => '20150622',
                'materieleRegistratie' => ['datumAanvang' => '20150101'],
                'statutaireNaam' => 'Stichting Bolderbast',
                'handelsnamen' => [['naam' => 'Test BV Donald', 'volgorde' => 1]],
                'sbiActiviteiten' => [['sbiCode' => '86101', 'sbiOmschrijving' => 'Universitair medisch centra', 'indHoofdactiviteit' => 'Ja']],
                'links' => [['rel' => 'self', 'href' => 'https://api.kvk.nl/api/v1/basisprofielen/69599068']],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/basisprofielen/69599068', []])
            ->willReturn($apiResponse);

        $repository = new BaseProfileRepository($gateway, '69599068');
        $result = $repository->get();

        self::assertInstanceOf(BaseProfileResponse::class, $result);
        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame('Test Stichting Bolderbast', $result->name);
        self::assertSame('Stichting Bolderbast', $result->statutoryName);
    }

    public function test_owner_calls_gateway_and_returns_response(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'rsin' => '123456789',
                'rechtsvorm' => 'BesloteVennootschap',
                'uitgebreideRechtsvorm' => 'Besloten Vennootschap met gewone structuur',
                'adressen' => [['type' => 'bezoekadres', 'straatnaam' => 'Watermolenlaan', 'huisnummer' => 1, 'postcode' => '3447GT', 'plaats' => 'Woerden']],
                'websites' => ['https://example.com'],
                'links' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/basisprofielen/69599068/eigenaar', []])
            ->willReturn($apiResponse);

        $repository = new BaseProfileRepository($gateway, '69599068');
        $result = $repository->owner();

        self::assertInstanceOf(BaseProfileOwnerResponse::class, $result);
        self::assertSame('123456789', $result->rsin);
        self::assertSame('BesloteVennootschap', $result->legalForm);
    }

    public function test_main_branch_calls_gateway_and_returns_response(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'vestigingsnummer' => '000037178598',
                'kvkNummer' => '69599068',
                'rsin' => '123456789',
                'indNonMailing' => 'Nee',
                'eersteHandelsnaam' => 'Test BV Donald',
                'indHoofdvestiging' => 'Ja',
                'indCommercieleVestiging' => 'Ja',
                'voltijdWerkzamePersonen' => 10,
                'totaalWerkzamePersonen' => 15,
                'deeltijdWerkzamePersonen' => 5,
                'handelsnamen' => [['naam' => 'Test BV Donald', 'volgorde' => 1]],
                'adressen' => [['type' => 'bezoekadres', 'straatnaam' => 'Watermolenlaan', 'huisnummer' => 1, 'postcode' => '3447GT', 'plaats' => 'Woerden']],
                'websites' => [],
                'sbiActiviteiten' => [],
                'links' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/basisprofielen/69599068/hoofdvestiging', []])
            ->willReturn($apiResponse);

        $repository = new BaseProfileRepository($gateway, '69599068');
        $result = $repository->mainBranch();

        self::assertInstanceOf(BaseProfileMainBranchResponse::class, $result);
        self::assertSame('000037178598', $result->branchNumber);
        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame('Test BV Donald', $result->firstTradeName);
    }

    public function test_branches_calls_gateway_and_returns_result(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'aantalCommercieleVestigingen' => 1,
                'aantalNietCommercieleVestigingen' => 0,
                'totaalAantalVestigingen' => 1,
                'vestigingen' => [
                    [
                        'vestigingsnummer' => '000037178598',
                        'eersteHandelsnaam' => 'Test BV Donald',
                        'indHoofdvestiging' => 'Ja',
                        'indCommercieleVestiging' => 'Ja',
                        'volledigAdres' => 'Hizzaarderlaan 1 1234AB Lollum',
                        'links' => [],
                    ],
                ],
                'links' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/basisprofielen/69599068/vestigingen', []])
            ->willReturn($apiResponse);

        $repository = new BaseProfileRepository($gateway, '69599068');
        $result = $repository->branches();

        self::assertInstanceOf(BaseProfileBranchesResult::class, $result);
        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame(1, $result->totalBranchCount);
        self::assertCount(1, $result->branches);
    }

    public function test_geo_data_passes_parameter_to_gateway(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'indNonMailing' => 'Ja',
                'naam' => 'Test Stichting Bolderbast',
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/basisprofielen/69599068', ['geoData' => true]])
            ->willReturn($apiResponse);

        $repository = new BaseProfileRepository($gateway, '69599068');
        $repository->geoData(true)->get();
    }

    public function test_geo_data_returns_self(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $repository = new BaseProfileRepository($gateway, '69599068');

        self::assertSame($repository, $repository->geoData());
    }

    public function test_default_passes_empty_query_params(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'indNonMailing' => 'Ja',
                'naam' => 'Test Stichting Bolderbast',
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v1/basisprofielen/69599068', []])
            ->willReturn($apiResponse);

        $repository = new BaseProfileRepository($gateway, '69599068');
        $repository->get();
    }
}
