<?php

namespace Vigilant\Healthchecks\Checks;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;

class Endpoint extends Checker
{
    public function check(Healthcheck $healthcheck): int
    {
        throw_if($healthcheck->endpoint === null, InvalidArgumentException::class, 'Healthcheck endpoint is not defined');

        $timeout = config('healthchecks.http_timeout', 10);
        $runId = $this->generateRunId($healthcheck);

        try {
            $response = $this->performHttpCall(
                $healthcheck,
                function (PendingRequest $request) use ($timeout, $healthcheck): Response {
                    return $request
                        ->timeout($timeout)
                        ->get($healthcheck->endpoint);
                }
            );

            $healthy = $response->ok();

            $status = $healthy ? Status::Healthy : Status::Unhealthy;
            $message = $healthy
                ? __('Endpoint is reachable')
                : __('Endpoint returned status code :code', ['code' => $response->status()]);

            $this->persistResult($healthcheck, 'endpoint_check', $status, $message);
        } catch (ConnectionException $e) {
            $this->persistResult(
                $healthcheck,
                'connection',
                Status::Unhealthy,
                'Failed to connect to endpoint',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }

        return $runId;
    }
}
