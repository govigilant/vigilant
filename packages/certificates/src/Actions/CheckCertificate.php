<?php

namespace Vigilant\Certificates\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Certificates\Notifications\CertificateChangedNotification;
use Vigilant\Certificates\Notifications\CertificateExpiredNotification;
use Vigilant\Certificates\Notifications\CertificateExpiresInDaysNotification;
use Vigilant\Certificates\Notifications\UnableToResolveCertificateNotification;

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
            UnableToResolveCertificateNotification::notify($monitor, $errstr);

            return;
        }

        $metadata = stream_get_meta_data($client);

        $contParams = stream_context_get_params($client);
        $certificate = openssl_x509_parse($contParams['options']['ssl']['peer_certificate']);

        if ($certificate === false) {
            UnableToResolveCertificateNotification::notify($monitor, 'Unable to parse certificate');

            return;
        }

        $fingerprint = openssl_x509_fingerprint($contParams['options']['ssl']['peer_certificate'], 'sha256');

        $validTo = Carbon::createFromTimestampUTC(data_get($certificate, 'validTo_time_t'));

        if ($validTo->isAfter(now()->addDays(30))) {
            $nextCheck = now()->addDays(30);
        } elseif ($validTo->isAfter(now()->addDays(7))) {
            $nextCheck = now()->addDays(7);
        } else {
            $nextCheck = $validTo->subDay();

            if ($nextCheck->isPast()) {
                $nextCheck = now()->addHours(3);
            }
        }

        if ($validTo->isPast()) {
            CertificateExpiredNotification::notify($monitor);
        } else {

        }

        if ($monitor->fingerprint !== $fingerprint) {
            $history = $monitor->history()->create([
                'serial_number' => data_get($certificate, 'serialNumber'),
                'protocol' => data_get($metadata, 'crypto.protocol'),
                'fingerprint' => $fingerprint,
                'valid_from' => Carbon::createFromTimestampUTC(data_get($certificate, 'validFrom_time_t')),
                'valid_to' => $validTo,
                'data' => array_merge(
                    $certificate,
                    [
                        'metadata' => $metadata,
                    ]
                ),
            ]);

            CertificateChangedNotification::notify($monitor, $history);
        }

        $monitor->update([
            'next_check' => $nextCheck,
            'serial_number' => data_get($certificate, 'serialNumber'),
            'protocol' => data_get($metadata, 'crypto.protocol'),
            'fingerprint' => $fingerprint,
            'valid_from' => Carbon::createFromTimestampUTC(data_get($certificate, 'validFrom_time_t')),
            'valid_to' => $validTo,
            'data' => array_merge(
                $certificate,
                [
                    'metadata' => $metadata,
                ]
            ),
        ]);

        if ($validTo->isFuture()) {
            CertificateExpiresInDaysNotification::notify($monitor);
        }
    }
}
