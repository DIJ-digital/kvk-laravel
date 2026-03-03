<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Parameters\SearchParameters;
use PHPUnit\Framework\TestCase;

final class SearchParametersTest extends TestCase
{
    public function test_constructor_defaults(): void
    {
        $params = new SearchParameters;

        self::assertNull($params->kvkNumber);
        self::assertNull($params->RSIN);
        self::assertNull($params->branchNumber);
        self::assertNull($params->name);
        self::assertNull($params->streetName);
        self::assertNull($params->city);
        self::assertNull($params->postalCode);
        self::assertNull($params->houseNumber);
        self::assertNull($params->houseLetter);
        self::assertNull($params->poBoxNumber);
        self::assertNull($params->type);
        self::assertNull($params->includeInactiveRegistrations);
        self::assertSame(1, $params->page);
        self::assertSame(100, $params->resultsPerPage);
    }

    public function test_constructor_with_all_parameters(): void
    {
        $params = new SearchParameters(
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
            type: ['hoofdvestiging', 'nevenvestiging'],
            includeInactiveRegistrations: true,
            page: 2,
            resultsPerPage: 50,
        );

        self::assertSame('69599068', $params->kvkNumber);
        self::assertSame('123456789', $params->RSIN);
        self::assertSame('000037178598', $params->branchNumber);
        self::assertSame('Test BV', $params->name);
        self::assertSame('Hizzaarderlaan', $params->streetName);
        self::assertSame('Lollum', $params->city);
        self::assertSame('1234AB', $params->postalCode);
        self::assertSame(1, $params->houseNumber);
        self::assertSame('A', $params->houseLetter);
        self::assertSame(123, $params->poBoxNumber);
        self::assertSame(['hoofdvestiging', 'nevenvestiging'], $params->type);
        self::assertTrue($params->includeInactiveRegistrations);
        self::assertSame(2, $params->page);
        self::assertSame(50, $params->resultsPerPage);
    }

    public function test_to_array_maps_english_to_dutch(): void
    {
        $params = new SearchParameters(
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

        $array = $params->toArray();

        self::assertSame('69599068', $array['kvkNummer']);
        self::assertSame('123456789', $array['rsin']);
        self::assertSame('000037178598', $array['vestigingsnummer']);
        self::assertSame('Test BV', $array['naam']);
        self::assertSame('Hizzaarderlaan', $array['straatnaam']);
        self::assertSame('Lollum', $array['plaats']);
        self::assertSame('1234AB', $array['postcode']);
        self::assertSame(1, $array['huisnummer']);
        self::assertSame('A', $array['huisletter']);
        self::assertSame(123, $array['postbusnummer']);
        self::assertSame(['hoofdvestiging'], $array['type']);
        self::assertTrue($array['inclusiefInactieveRegistraties']);
        self::assertSame(2, $array['pagina']);
        self::assertSame(50, $array['resultatenPerPagina']);
    }

    public function test_to_array_filters_null_values(): void
    {
        $params = new SearchParameters;

        $array = $params->toArray();

        self::assertArrayNotHasKey('kvkNummer', $array);
        self::assertArrayNotHasKey('rsin', $array);
        self::assertArrayNotHasKey('vestigingsnummer', $array);
        self::assertArrayNotHasKey('naam', $array);
        self::assertArrayNotHasKey('straatnaam', $array);
        self::assertArrayNotHasKey('plaats', $array);
        self::assertArrayNotHasKey('postcode', $array);
        self::assertArrayNotHasKey('huisnummer', $array);
        self::assertArrayNotHasKey('huisletter', $array);
        self::assertArrayNotHasKey('postbusnummer', $array);
        self::assertArrayNotHasKey('type', $array);
        self::assertArrayNotHasKey('inclusiefInactieveRegistraties', $array);
        self::assertArrayHasKey('pagina', $array);
        self::assertArrayHasKey('resultatenPerPagina', $array);
    }

    public function test_to_array_keeps_false_values(): void
    {
        $params = new SearchParameters(includeInactiveRegistrations: false);

        $array = $params->toArray();

        self::assertArrayHasKey('inclusiefInactieveRegistraties', $array);
        self::assertFalse($array['inclusiefInactieveRegistraties']);
    }
}
