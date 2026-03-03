<?php

declare(strict_types=1);

namespace DIJ\Kvk\Exceptions;

use RuntimeException;

class KvkException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly string $responseBody,
    ) {
        parent::__construct($message, $statusCode);
    }
}
