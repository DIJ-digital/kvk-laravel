<?php

declare(strict_types=1);

namespace DIJ\Kvk\Repositories;

use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\KVKGateway;

class NamingRepository
{
    public function __construct(
        protected KVKGateway $gateway,
        private readonly string $kvkNumber,
    ) {}

    public function get(): NamingResponse
    {
        $result = $this->gateway->get("api/v1/naamgevingen/kvknummer/{$this->kvkNumber}");

        return NamingResponse::fromResponse($result);
    }
}
