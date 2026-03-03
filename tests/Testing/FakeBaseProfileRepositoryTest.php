<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Data\Responses\BaseProfileMainBranchResponse;
use DIJ\Kvk\Data\Responses\BaseProfileOwnerResponse;
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Results\BaseProfileBranchesResult;
use DIJ\Kvk\Testing\FakeBaseProfileRepository;
use PHPUnit\Framework\TestCase;

final class FakeBaseProfileRepositoryTest extends TestCase
{
    public function test_get_returns_base_profile_response(): void
    {
        $repo = new FakeBaseProfileRepository(BaseProfileResponse::fake(), BaseProfileOwnerResponse::fake(), BaseProfileMainBranchResponse::fake(), BaseProfileBranchesResult::fake());
        $result = $repo->get();
        self::assertInstanceOf(BaseProfileResponse::class, $result);
    }

    public function test_owner_returns_base_profile_owner_response(): void
    {
        $repo = new FakeBaseProfileRepository(BaseProfileResponse::fake(), BaseProfileOwnerResponse::fake(), BaseProfileMainBranchResponse::fake(), BaseProfileBranchesResult::fake());
        $result = $repo->owner();
        self::assertInstanceOf(BaseProfileOwnerResponse::class, $result);
    }

    public function test_main_branch_returns_base_profile_main_branch_response(): void
    {
        $repo = new FakeBaseProfileRepository(BaseProfileResponse::fake(), BaseProfileOwnerResponse::fake(), BaseProfileMainBranchResponse::fake(), BaseProfileBranchesResult::fake());
        $result = $repo->mainBranch();
        self::assertInstanceOf(BaseProfileMainBranchResponse::class, $result);
    }

    public function test_branches_returns_base_profile_branches_result(): void
    {
        $repo = new FakeBaseProfileRepository(BaseProfileResponse::fake(), BaseProfileOwnerResponse::fake(), BaseProfileMainBranchResponse::fake(), BaseProfileBranchesResult::fake());
        $result = $repo->branches();
        self::assertInstanceOf(BaseProfileBranchesResult::class, $result);
    }

    public function test_geo_data_returns_self(): void
    {
        $repo = new FakeBaseProfileRepository(BaseProfileResponse::fake(), BaseProfileOwnerResponse::fake(), BaseProfileMainBranchResponse::fake(), BaseProfileBranchesResult::fake());
        self::assertSame($repo, $repo->geoData());
    }

    public function test_geo_data_with_false_returns_self(): void
    {
        $repo = new FakeBaseProfileRepository(BaseProfileResponse::fake(), BaseProfileOwnerResponse::fake(), BaseProfileMainBranchResponse::fake(), BaseProfileBranchesResult::fake());
        self::assertSame($repo, $repo->geoData(false));
    }
}
