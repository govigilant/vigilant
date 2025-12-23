<?php

namespace Vigilant\Healthchecks\Checks;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Validator;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;

class Module extends Checker
{
    public function check(Healthcheck $healthcheck): int
    {
        $runId = $this->generateRunId($healthcheck);

        $endpoint = blank($healthcheck->endpoint) ? $healthcheck->type->endpoint() : $healthcheck->endpoint;

        throw_if($endpoint === null, 'Endpoint is required');

        try {
            $response = $this->performHttpCall(
                $healthcheck,
                function (PendingRequest $request) use ($healthcheck, $endpoint): Response {
                    return $request
                        ->withToken($healthcheck->token)
                        ->post($endpoint);
                }
            );
        } catch (ConnectionException) {
            $this->persistResult($healthcheck, 'connection', Status::Unhealthy, 'Could not connect');

            return $runId;
        }

        if ($response->failed()) {
            $this->persistResult($healthcheck, 'connection', Status::Unhealthy, 'Failed to check health, status: '.$response->status());

            return $runId;
        }

        $this->persistResult($healthcheck, 'connection', Status::Healthy);

        $checks = $response->json($healthcheck->type->checksResponseKey(), []);
        $metrics = $response->json($healthcheck->type->metricsResponseKey(), []);

        foreach ($checks as $check) {
            $validator = Validator::make($check, [
                'type' => ['required', 'string'],
                'key' => ['nullable', 'string'],
                'status' => ['required', 'string', 'in:healthy,unhealthy,failed'],
                'message' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                continue;
            }

            $status = Status::from($check['status']);

            $key = $check['type'];

            if (! blank($check['key'] ?? null)) {
                $key .= '_'.$check['key'];
            }

            $this->persistResult($healthcheck, str($key)->limit(255)->toString(), $status, $check['message'] ?? null);
        }

        foreach ($metrics as $metric) {
            $validator = Validator::make($metric, [
                'type' => ['required', 'string'],
                'value' => ['required', 'numeric'],
                'unit' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                continue;
            }

            $healthcheck->metrics()->create([
                'run_id' => $runId,
                'key' => $metric['type'],
                'value' => $metric['value'],
                'unit' => $metric['unit'],
            ]);
        }

        return $runId;
    }
}
