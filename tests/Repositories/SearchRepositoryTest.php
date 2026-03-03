<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use DIJ\Kvk\Data\Parameters\SearchParameters;
use DIJ\Kvk\Data\Results\SearchResult;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\Repositories\SearchRepository;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class SearchRepositoryTest extends TestCase
{
    public function test_search_calls_gateway_and_returns_result(): void
    {
        $parameters = new SearchParameters(name: 'Test BV', page: 1, resultsPerPage: 10);

        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'resultatenPerPagina' => 10,
                'totaal' => 1,
                'resultaten' => [
                    [
                        'kvkNummer' => '69599068',
                        'naam' => 'Test BV Donald',
                        'type' => 'hoofdvestiging',
                        'actief' => 'Ja',
                        'vestigingsnummer' => '000037178598',
                        'adres' => [
                            'binnenlandsAdres' => [
                                'type' => 'bezoekadres',
                                'straatnaam' => 'Hizzaarderlaan',
                                'huisnummer' => 1,
                                'postcode' => '1234AB',
                                'plaats' => 'Lollum',
                            ],
                        ],
                        'links' => [
                            [
                                'rel' => 'basisprofiel',
                                'href' => 'https://api.kvk.nl/api/v1/basisprofielen/69599068',
                            ],
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v2/zoeken', $parameters->toArray()])
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $result = $repository->search($parameters);

        self::assertInstanceOf(SearchResult::class, $result);
        self::assertCount(1, $result->items);
        self::assertSame('69599068', $result->items->first()->kvkNumber);
        self::assertSame('Test BV Donald', $result->items->first()->name);
        self::assertSame(1, $result->page);
        self::assertSame(1, $result->total);
    }

    public function test_search_with_empty_results(): void
    {
        $parameters = new SearchParameters(kvkNumber: '99999999');

        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'resultatenPerPagina' => 10,
                'totaal' => 0,
                'resultaten' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createStub(KVKGateway::class);
        $gateway->method('__call')
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $result = $repository->search($parameters);

        self::assertCount(0, $result->items);
        self::assertSame(0, $result->total);
    }

    public function test_fluent_setters_return_self(): void
    {
        $gateway = $this->createStub(KVKGateway::class);
        $repository = new SearchRepository($gateway);

        self::assertSame($repository, $repository->kvkNumber('69599068'));
        self::assertSame($repository, $repository->rsin('123456789'));
        self::assertSame($repository, $repository->branchNumber('000037178598'));
        self::assertSame($repository, $repository->name('Test BV'));
        self::assertSame($repository, $repository->streetName('Hizzaarderlaan'));
        self::assertSame($repository, $repository->city('Lollum'));
        self::assertSame($repository, $repository->postalCode('1234AB'));
        self::assertSame($repository, $repository->houseNumber(1));
        self::assertSame($repository, $repository->houseLetter('A'));
        self::assertSame($repository, $repository->poBoxNumber(123));
        self::assertSame($repository, $repository->type(['hoofdvestiging']));
        self::assertSame($repository, $repository->includeInactiveRegistrations());
        self::assertSame($repository, $repository->page(2));
        self::assertSame($repository, $repository->resultsPerPage(50));
    }

    public function test_get_with_all_parameters_calls_gateway(): void
    {
        $expectedParams = new SearchParameters(
            kvkNumber: '69599068',
            RSIN: '123456789',
            branchNumber: '000037178598',
            name: 'Test BV',
            streetName: 'Hizzaarderlaan',
            city: 'Lollum',
            postalCode: '1234AB',
            houseNumber: 1,
            houseLetter: 'A',
            poBoxNumber: 123,
            type: ['hoofdvestiging'],
            includeInactiveRegistrations: true,
            page: 2,
            resultsPerPage: 50,
        );

        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 2,
                'resultatenPerPagina' => 50,
                'totaal' => 0,
                'resultaten' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v2/zoeken', $expectedParams->toArray()])
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $result = $repository
            ->kvkNumber('69599068')
            ->rsin('123456789')
            ->branchNumber('000037178598')
            ->name('Test BV')
            ->streetName('Hizzaarderlaan')
            ->city('Lollum')
            ->postalCode('1234AB')
            ->houseNumber(1)
            ->houseLetter('A')
            ->poBoxNumber(123)
            ->type(['hoofdvestiging'])
            ->includeInactiveRegistrations()
            ->page(2)
            ->resultsPerPage(50)
            ->get();

        self::assertInstanceOf(SearchResult::class, $result);
    }

    public function test_get_with_defaults_passes_page_and_results_per_page(): void
    {
        $expectedParams = new SearchParameters(page: 1, resultsPerPage: 100);

        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'resultatenPerPagina' => 100,
                'totaal' => 0,
                'resultaten' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v2/zoeken', $expectedParams->toArray()])
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $result = $repository->get();

        self::assertInstanceOf(SearchResult::class, $result);
    }

    public function test_get_fluent_chain_returns_result(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'resultatenPerPagina' => 100,
                'totaal' => 1,
                'resultaten' => [
                    [
                        'kvkNummer' => '69599068',
                        'naam' => 'Test BV Donald',
                        'type' => 'hoofdvestiging',
                        'actief' => 'Ja',
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createStub(KVKGateway::class);
        $gateway->method('__call')
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $result = $repository->kvkNumber('69599068')->city('Amsterdam')->get();

        self::assertInstanceOf(SearchResult::class, $result);
        self::assertCount(1, $result->items);
        self::assertSame('69599068', $result->items->first()->kvkNumber);
    }

    public function test_include_inactive_registrations_defaults_to_true(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'resultatenPerPagina' => 100,
                'totaal' => 0,
                'resultaten' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v2/zoeken', (new SearchParameters(includeInactiveRegistrations: true))->toArray()])
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $repository->includeInactiveRegistrations()->get();
    }

    public function test_include_inactive_registrations_can_be_set_to_false(): void
    {
        $apiResponse = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'pagina' => 1,
                'resultatenPerPagina' => 100,
                'totaal' => 0,
                'resultaten' => [],
            ], JSON_THROW_ON_ERROR)),
        );

        $gateway = $this->createMock(KVKGateway::class);
        $gateway->expects(self::once())
            ->method('__call')
            ->with('get', ['api/v2/zoeken', (new SearchParameters(includeInactiveRegistrations: false))->toArray()])
            ->willReturn($apiResponse);

        $repository = new SearchRepository($gateway);
        $repository->includeInactiveRegistrations(false)->get();
    }
}
