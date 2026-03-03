<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Collections;

use DIJ\Kvk\Collections\SearchResponseCollection;
use DIJ\Kvk\Data\Responses\SearchResponse;
use PHPUnit\Framework\TestCase;

final class SearchResponseCollectionTest extends TestCase
{
    public function test_collection_holds_typed_items(): void
    {
        $item = new SearchResponse(
            kvkNumber: '69599068',
            name: 'Test BV',
            type: 'hoofdvestiging',
            active: 'Ja',
        );

        $collection = new SearchResponseCollection([$item]);

        self::assertCount(1, $collection);
        self::assertInstanceOf(SearchResponse::class, $collection->first());
        self::assertSame('69599068', $collection->first()->kvkNumber);
    }

    public function test_collection_supports_chaining(): void
    {
        $items = [
            new SearchResponse(kvkNumber: '69599068', name: 'Active BV', type: 'hoofdvestiging', active: 'Ja'),
            new SearchResponse(kvkNumber: '68750110', name: 'Inactive BV', type: 'hoofdvestiging', active: 'Nee'),
        ];

        $collection = new SearchResponseCollection($items);
        $filtered = $collection->filter(fn (SearchResponse $item): bool => $item->active === 'Ja');

        self::assertCount(2, $collection);
        self::assertCount(1, $filtered);
        self::assertInstanceOf(SearchResponseCollection::class, $filtered);
    }
}
