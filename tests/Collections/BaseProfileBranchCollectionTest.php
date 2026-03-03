<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Collections;

use DIJ\Kvk\Collections\BaseProfileBranchCollection;
use DIJ\Kvk\Data\Responses\BaseProfileBranchResponse;
use PHPUnit\Framework\TestCase;

final class BaseProfileBranchCollectionTest extends TestCase
{
    public function test_collection_contains_branch_response_instances(): void
    {
        $collection = new BaseProfileBranchCollection([BaseProfileBranchResponse::fake()]);

        $first = $collection->first();

        self::assertInstanceOf(BaseProfileBranchResponse::class, $first);
        self::assertSame('000037178598', $first->branchNumber);
    }
}
