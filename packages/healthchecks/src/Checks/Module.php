<?php

namespace Vigilant\Healthchecks\Checks;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;

class Module extends Checker
{
    public function check(Healthcheck $healthcheck): int
    {
        $runId = null;

        for ($i = 0; $i < 10; $i++) {
            $candidate = rand(1, 100000);

            $exists = $healthcheck->results()->where('run_id', '=', $candidate)->exists();

            if (! $exists) {
                $runId = $candidate;
                break;
            }
        }

        throw_if($runId === null, 'Could not generate unique run ID');

        $endpoint = $healthcheck->endpoint ?? $healthcheck->type->endpoint();

        throw_if($endpoint === null, 'Endpoint is required');

        try {
            $response = Http::baseUrl($healthcheck->domain)
                ->withToken($healthcheck->token)
                ->post($endpoint);
        } catch (ConnectionException) {
            $healthcheck->results()->create([
                'run_id' => $runId,
                'key' => 'connection',
                'status' => Status::Unhealthy,
                'message' => 'Could not connect',
            ]);

            return $runId;
        }

        $checks = $response->json('checks', []);
        $metrics = $response->json('metrics', []);

        foreach ($checks as $check) {
            $validator = Validator::make($check, [
                'key' => ['required', 'string'],
                'status' => ['required', 'string', 'in:healthy,unhealthy,failed'],
                'message' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                continue;
            }

            $status = Status::from($check['status']);

            $healthcheck->results()->create([
                'run_id' => $runId,
                'key' => $check['key'],
                'status' => $status,
                'message' => $check['message'] ?? null,
            ]);
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
