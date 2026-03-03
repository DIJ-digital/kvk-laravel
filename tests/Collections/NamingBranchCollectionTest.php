<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Collections;

use DIJ\Kvk\Collections\NamingBranchCollection;
use DIJ\Kvk\Data\Responses\NamingBranchResponse;
use PHPUnit\Framework\TestCase;

final class NamingBranchCollectionTest extends TestCase
{
    public function test_collection_contains_naming_branch_response_instances(): void
    {
        $collection = new NamingBranchCollection([NamingBranchResponse::fake()]);

        $first = $collection->first();

        self::assertInstanceOf(NamingBranchResponse::class, $first);
        self::assertSame('000037178598', $first->branchNumber);
    }
}
