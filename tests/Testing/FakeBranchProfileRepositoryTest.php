<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\Testing\FakeBranchProfileRepository;
use PHPUnit\Framework\TestCase;

final class FakeBranchProfileRepositoryTest extends TestCase
{
    public function test_get_returns_branch_profile_response(): void
    {
        $repository = new FakeBranchProfileRepository(BranchProfileResponse::fake());
        $result = $repository->get();

        self::assertInstanceOf(BranchProfileResponse::class, $result);
    }

    public function test_geo_data_returns_self(): void
    {
        $repository = new FakeBranchProfileRepository(BranchProfileResponse::fake());

        self::assertSame($repository, $repository->geoData());
    }

    public function test_geo_data_with_false_returns_self(): void
    {
        $repository = new FakeBranchProfileRepository(BranchProfileResponse::fake());

        self::assertSame($repository, $repository->geoData(false));
    }
}
