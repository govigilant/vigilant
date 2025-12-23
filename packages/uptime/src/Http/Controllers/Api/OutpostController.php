<?php

namespace Vigilant\Uptime\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Vigilant\Frontend\Validation\CountryCode;
use Vigilant\Uptime\Actions\Outpost\GenerateOutpostCertificate;
use Vigilant\Uptime\Actions\Outpost\RegisterOutpost;
use Vigilant\Uptime\Models\Outpost;

class OutpostController extends Controller
{
    public function register(
        Request $request,
        RegisterOutpost $registrar,
        GenerateOutpostCertificate $certificateGenerator
    ): JsonResponse {
        $geoipAutomatic = ! ($request->filled('country') && $request->filled('latitude') && $request->filled('longitude'));

        $request->validate([
            'ip' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'country' => [
                'nullable',
                'string',
                new CountryCode,
                Rule::requiredIf(! $geoipAutomatic),
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
                Rule::requiredIf(! $geoipAutomatic),
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
                Rule::requiredIf(! $geoipAutomatic),
            ],
        ]);

        $clientIp = $request->ip();
        if ($clientIp === null) {
            return response()->json(['message' => 'Unable to determine client IP address.'], 400);
        }

        $outpost = $registrar->register(
            $request->input('ip'),
            $clientIp,
            $request->input('port'),
            $geoipAutomatic,
            $request->input('country'),
            $request->input('latitude'),
            $request->input('longitude')
        );

        // Generate a short-lived certificate for the outpost (valid for 30 days)
        $commonName = sprintf('outpost-%s-%d', $outpost->ip, $outpost->port);
        $certificate = $certificateGenerator->generate($commonName, $clientIp, 30);

        return response()->json($certificate);
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

    public function list(): JsonResponse
    {
        $outposts = Outpost::query()->get();

        return response()->json($outposts);
    }
}
