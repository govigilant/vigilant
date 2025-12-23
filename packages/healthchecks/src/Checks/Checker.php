<?php

namespace Vigilant\Healthchecks\Checks;

use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;

abstract class Checker
{
    /** @return int runId */
    abstract public function check(Healthcheck $healthcheck): int;

    protected function generateRunId(Healthcheck $healthcheck): int
    {
        for ($i = 0; $i < 10; $i++) {
            $candidate = rand(1, 100000);

            $exists = $healthcheck->metrics()->where('run_id', '=', $candidate)->exists();

            if (! $exists) {
                return $candidate;
            }
        }

        throw new RuntimeException('Could not generate unique run ID');
    }

    protected function persistResult(Healthcheck $healthcheck, string $key, Status $status, ?string $message = null, ?array $data = null): void
    {
        $attributes = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'last_checked_at' => now(),
        ];

        if ($status === Status::Unhealthy) {
            $attributes['last_unhealthy_at'] = now();
        }

        $healthcheck->results()->updateOrCreate(
            ['key' => $key],
            $attributes
        );
    }

    /**
     * @param  Closure(PendingRequest): Response  $callback
     * @throws ConnectionException
     */
    protected function performHttpCall(Healthcheck $healthcheck, Closure $callback): Response
    {
        $maxAttempts = max((int) config('healthchecks.http_max_attempts', 2), 1);

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $request = Http::baseUrl($healthcheck->domain);

                return $callback($request);
            } catch (ConnectionException $e) {
                if ($attempt === $maxAttempts) {
                    throw $e;
                }

                sleep(1);
            }
        }

        throw new RuntimeException('Unable to perform HTTP call');
    }
}
