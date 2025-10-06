<?php

namespace Vigilant\Uptime\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vigilant\Uptime\Actions\Outpost\RegisterOutpost;
use Vigilant\Uptime\Models\Outpost;

class OutpostController extends Controller
{
    public function register(Request $request, RegisterOutpost $registrar): JsonResponse
    {
        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        $registrar->register($request->input('ip'), $request->ip(), $request->input('port'));

        return response()->json(['message' => 'Outpost registered successfully.']);
    }

    public function unregister(Request $request): JsonResponse
    {
        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        Outpost::query()
            ->where('ip', $request->ip())
            ->where('external_ip', $request->input('ip'))
            ->where('port', $request->input('port'))
            ->delete();

        return response()->json(['message' => 'Outpost unregistered successfully.']);
    }
}
