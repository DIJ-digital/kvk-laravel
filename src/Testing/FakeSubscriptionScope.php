<?php

declare(strict_types=1);

namespace DIJ\Kvk\Testing;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;

final readonly class FakeSubscriptionScope
{
    public function __construct(
        private SignalsResult $signalsResult,
        private SignalResponse $signalResponse,
    ) {}

    public function from(string $from): self
    {
        return $this;
    }

    public function to(string $to): self
    {
        return $this;
    }

    public function page(int $page): self
    {
        return $this;
    }

    public function resultsPerPage(int $resultsPerPage): self
    {
        return $this;
    }

    public function signals(): SignalsResult
    {
        return $this->signalsResult;
    }

    public function signal(string $signalId): SignalResponse
    {
        return $this->signalResponse;
    }
}
