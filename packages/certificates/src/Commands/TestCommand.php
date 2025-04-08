<?php

namespace Vigilant\Certificates\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'certificates:test';

    public function handle()
    {
        $domain = 'govigilant.io';
        $port = 443;

        // Set up context to capture peer certificate
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        // Open connection
        $client = @stream_socket_client(
            "ssl://{$domain}:{$port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        // Get certificate
        $contParams = stream_context_get_params($client);
        $cert = openssl_x509_parse($contParams['options']['ssl']['peer_certificate']);

        dd($cert);
    }
}
