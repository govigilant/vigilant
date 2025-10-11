<?php

namespace Vigilant\Uptime\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class OutpostIpController extends Controller
{
    public function list(string $format): Response|JsonResponse
    {
        $ips = cache()->remember(
            'uptime:outposts:ips',
            now()->addMinutes(15),
            fn (): Collection => Outpost::query()
                ->select('external_ip')
                ->distinct()
                ->where('status', '=', OutpostStatus::Available)
                ->pluck('external_ip')
        );

        if ($format === 'text') {
            return response(
                $ips->implode("\n"),
                200,
                ['Content-Type' => 'text/plain']
            );
        }

        if ($format === 'json') {
            return response()->json($ips);
        }

        return response()->json(['message' => 'Invalid format. Use "text" or "json".'], 400);
    }
}
