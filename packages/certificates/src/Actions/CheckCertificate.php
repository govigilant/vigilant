<?php

namespace Vigilant\Certificates\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Certificates\Models\CertificateMonitor;

class CheckCertificate
{
    public function check(CertificateMonitor $monitor): void
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $client = @stream_socket_client(
            "ssl://{$monitor->domain}:{$monitor->port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if ($client === false) {
            dd($errno, $errstr);

            return;
        }

        $metadata = stream_get_meta_data($client);

        $contParams = stream_context_get_params($client);
        $certificate = openssl_x509_parse($contParams['options']['ssl']['peer_certificate']);

        $fingerprint = openssl_x509_fingerprint($contParams['options']['ssl']['peer_certificate'], 'sha256');

        $monitor->update([
            /* 'next_check' => now()->addDays(30), */
            'serial_number' => data_get($certificate, 'serialNumber'),
            'protocol' => data_get($metadata, 'crypto.protocol'),
            'fingerprint' => $fingerprint,
            'valid_from' => Carbon::createFromTimestampUTC(data_get($certificate, 'validFrom_time_t')),
            'valid_to' => Carbon::createFromTimestampUTC(data_get($certificate, 'validTo_time_t')),
            'data' => array_merge(
                $certificate,
                [
                    'metadata' => $metadata,
                ]
            ),

        ]);

    }
}
