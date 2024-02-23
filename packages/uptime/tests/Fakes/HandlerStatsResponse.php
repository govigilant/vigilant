<?php

namespace Vigilant\Uptime\Tests\Fakes;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;

class HandlerStatsResponse extends Response implements PromiseInterface
{
    protected $handlerStats = [];

    public function withHandlerStats(array $handlerStats)
    {
        $this->handlerStats = $handlerStats;
        return $this;
    }

    public function handlerStats()
    {
        return $this->handlerStats;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null): PromiseInterface
    {
        return $this;
    }

    public function otherwise(callable $onRejected): PromiseInterface
    {
        return $this;
    }

    public function getState(): string
    {
        return '';
    }

    public function resolve($value): void
    {
        //
    }

    public function reject($reason): void
    {
        //
    }

    public function cancel(): void
    {
        //
    }

    public function wait(bool $unwrap = true)
    {
        //
    }
}
