<?php

namespace Vigilant\Uptime\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vigilant\Uptime\Actions\Outpost\RegisterOutpost;

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
}
