<?php

namespace Vigilant\Dns\Client;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\Types\TCP;

class DnsClient
{
    public function get(string $record, int|array $type, int $attempt = 0): array
    {
        /** @var int $maxAttempts */
        $maxAttempts = config('dns.max_attempts', 3);

        if ($attempt > $maxAttempts) {
            return [];
        }

        $dnsHandler = (new TCP)
            ->setNameserver($this->getNameserver())
            ->setTimeout(3);

        $dnsRecordsService = new DnsRecords($dnsHandler);

        try {
            return $dnsRecordsService->get($record, $type);
        } catch (DnsHandlerException $e) {
            logger()->error("Failed to retrieve DNS record $record on attempt $attempt: ".$e->getMessage());

            return $this->get($record, $type, $attempt + 1);
        }
    }

    protected function getNameserver(): string
    {
        /** @var ?string $nameservers */
        $nameservers = config('dns.nameservers');

        return str($nameservers)->explode(',')->random();
    }
}
