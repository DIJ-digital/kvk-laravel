<?php

declare(strict_types=1);

namespace DIJ\Kvk;

use DIJ\Kvk\Data\Settings;
use DIJ\Kvk\Exceptions\KvkAuthenticationException;
use DIJ\Kvk\Exceptions\KvkRequestException;
use DIJ\Kvk\Exceptions\KvkServerException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

/**
 * @mixin PendingRequest
 */
class KVKGateway
{
    protected PendingRequest $http;

    public function __construct(
        Factory $factory,
        protected Settings $settings,
    ) {
        $this->http = $this->newClient($factory);
    }

    /**
     * @param  array<string,mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $result = $this->http->{$name}(...$arguments); // @phpstan-ignore method.dynamicName

        if ($result instanceof Response && ! $result->successful()) {
            $this->throwForStatus($result);
        }

        return $result;
    }

    protected function newClient(Factory $factory): PendingRequest
    {
        return $factory
            ->baseUrl($this->settings->base_url)
            ->withHeader('apikey', $this->settings->api_key);
    }

    /**
     * @return never
     */
    private function throwForStatus(Response $response): void
    {
        $status = $response->status();
        $body = $response->body();

        throw match (true) {
            $status === 401, $status === 403 => new KvkAuthenticationException(
                "KVK API authentication failed (HTTP {$status})",
                $status,
                $body,
            ),
            $status >= 500 => new KvkServerException(
                "KVK API server error (HTTP {$status})",
                $status,
                $body,
            ),
            default => new KvkRequestException(
                "KVK API request failed (HTTP {$status})",
                $status,
                $body,
            ),
        };
    }
}
