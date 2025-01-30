<?php

namespace Vigilant\Dns\Client;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\Types\TCP;

class DnsClient
{
    public function get(string $record, int|array $type): array
    {
        $dnsHandler = (new TCP)
            ->setNameserver(config('dns.nameserver'));

        $dnsRecordsService = new DnsRecords($dnsHandler);

        try {
            return $dnsRecordsService->get($record, $type);
        } catch (DnsHandlerException $e) {
            return [];
        }
    }
}
