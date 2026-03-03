<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Data\Responses\NamingResponse;

final readonly class FakeNamingRepository
{
    public function __construct(
        private NamingResponse $response,
    ) {}

    public function get(): NamingResponse
    {
        return $this->response;
    }
}
