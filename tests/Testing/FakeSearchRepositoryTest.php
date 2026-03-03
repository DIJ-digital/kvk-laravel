<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Collections\SearchResponseCollection;
use DIJ\Kvk\Data\Parameters\SearchParameters;
use DIJ\Kvk\Data\Results\SearchResult;
use DIJ\Kvk\Testing\FakeSearchRepository;
use PHPUnit\Framework\TestCase;

final class FakeSearchRepositoryTest extends TestCase
{
    public function test_get_returns_configured_result(): void
    {
        $result = new SearchResult(new SearchResponseCollection([]), 1, 10, 0);
        $repository = new FakeSearchRepository($result);

        self::assertSame($result, $repository->get());
    }

    public function test_search_returns_configured_result(): void
    {
        $result = new SearchResult(new SearchResponseCollection([]), 1, 10, 0);
        $repository = new FakeSearchRepository($result);
        $parameters = new SearchParameters;

        self::assertSame($result, $repository->search($parameters));
    }

    public function test_fluent_setters_return_self(): void
    {
        $result = new SearchResult(new SearchResponseCollection([]), 1, 10, 0);
        $repository = new FakeSearchRepository($result);

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

    public function test_get_returns_same_result_on_multiple_calls(): void
    {
        $result = new SearchResult(new SearchResponseCollection([]), 1, 10, 0);
        $repository = new FakeSearchRepository($result);

        $firstCall = $repository->get();
        $secondCall = $repository->get();

        self::assertSame($firstCall, $secondCall);
        self::assertSame($result, $firstCall);
    }

    public function test_fluent_chain_returns_result(): void
    {
        $result = new SearchResult(new SearchResponseCollection([]), 1, 10, 0);
        $repository = new FakeSearchRepository($result);

        $chainedResult = $repository
            ->kvkNumber('69599068')
            ->city('Amsterdam')
            ->name('Test BV')
            ->get();

        self::assertSame($result, $chainedResult);
    }
}
