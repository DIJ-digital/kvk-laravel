<?php

declare(strict_types=1);

namespace DIJ\Kvk\Repositories;

use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\KVKGateway;

class SubscriptionScope
{
    private ?string $from = null;

    private ?string $to = null;

    private ?int $page = null;

    private ?int $resultsPerPage = null;

    public function __construct(
        protected KVKGateway $gateway,
        private readonly string $subscriptionId,
    ) {}

    public function from(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function to(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function resultsPerPage(int $resultsPerPage): self
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }

    /**
     * @return array<string, int|string>
     */
    private function queryParams(): array
    {
        $params = [];

        if ($this->from !== null) {
            $params['vanaf'] = $this->from;
        }

        if ($this->to !== null) {
            $params['tot'] = $this->to;
        }

        if ($this->page !== null) {
            $params['pagina'] = $this->page;
        }

        if ($this->resultsPerPage !== null) {
            $params['aantal'] = $this->resultsPerPage;
        }

        return $params;
    }

    public function signals(): SignalsResult
    {
        $result = $this->gateway->get(
            "api/v1/abonnementen/{$this->subscriptionId}",
            $this->queryParams(),
        );

        return SignalsResult::fromResponse($result);
    }

    public function signal(string $signalId): SignalResponse
    {
        $result = $this->gateway->get(
            "api/v1/abonnementen/{$this->subscriptionId}/signalen/{$signalId}",
        );

        return SignalResponse::fromResponse($result);
    }
}
