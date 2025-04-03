<?php

namespace Vigilant\Dns\Client;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\Types\UDP;

class DnsClient
{
    public function get(string $record, int|array $type, int $attempt = 0): array
    {
        $maxAttempts = config()->integer('dns.max_attempts', 3);

        if ($attempt > $maxAttempts) {
            return [];
        }

        $nameServer = $this->getNameserver();

        $dnsHandler = (new UDP)
            ->setNameserver($nameServer)
            ->setTimeout(3);

        $dnsRecordsService = new DnsRecords($dnsHandler);

        try {
            $result = $dnsRecordsService->get($record, $type);
        } catch (DnsHandlerException $e) {
            logger()->error("Failed to retrieve DNS record $record on attempt $attempt with nameserer $nameServer: ".$e->getMessage().' '.$e->getTraceAsString());

            return $this->get($record, $type, $attempt + 1);
        }

        if (count($result) === 0 && $attempt < $maxAttempts) {
            return $this->get($record, $type, $attempt + 1);
        }

        return $result;
    }

    protected function getNameserver(): string
    {
        $nameservers = config()->string('dns.nameservers');

        return str($nameservers)->explode(',')->random();
    }
}
