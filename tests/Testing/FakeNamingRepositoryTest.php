<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Testing;

use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\Testing\FakeNamingRepository;
use PHPUnit\Framework\TestCase;

final class FakeNamingRepositoryTest extends TestCase
{
    public function test_get_returns_naming_response(): void
    {
        $repository = new FakeNamingRepository(NamingResponse::fake());
        $result = $repository->get();

        self::assertInstanceOf(NamingResponse::class, $result);
    }
}
