<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Actions\Outpost\GenerateRootCertificate;

class GenerateRootCaCommand extends Command
{
    protected $signature = 'uptime:generate-root-ca';

    protected $description = 'Generate root CA certificate for outpost HTTPS';

    public function handle(GenerateRootCertificate $generator): int
    {
        if ($generator->exists()) {
            $this->info('Root CA certificate already exists.');

            return static::SUCCESS;
        }

        $this->info('Generating root CA certificate...');

        $generator->generate();

        $this->info('Root CA certificate generated successfully.');

        return static::SUCCESS;
    }
}
